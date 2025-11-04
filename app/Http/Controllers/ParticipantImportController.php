<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Participant;
use App\Models\ParticipantType;
use App\Models\User;
use App\Models\TravelDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * ParticipantImportController
 * 
 * This controller handles bulk participant imports via CSV files.
 * 
 * Engineering Approach:
 * - Uses transaction-based processing to ensure data integrity
 * - Implements detailed validation at both file and row level
 * - Provides comprehensive error reporting for failed imports
 * - Handles user creation/update intelligently (checks if email exists)
 * 
 * Why this design?
 * 1. Transactions: Ensures all-or-nothing approach prevents partial data corruption
 * 2. Row-by-row validation: Allows identification of specific problematic entries
 * 3. Error reporting: Users get clear feedback on what went wrong and where
 * 4. Email-based user matching: Prevents duplicate user accounts
 */
class ParticipantImportController extends Controller
{
    public function __construct()
    {
        // Permission-gated access for participant import actions
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) {
                abort(403, 'Unauthorized');
            }

            // Superadmins have access to everything
            if ($user->hasRole('superadmin')) {
                return $next($request);
            }

            $action = $request->route()->getActionMethod();
            $permissionMap = [
                'downloadSample' => 'participants.import.sample',
                'showImportForm' => 'participants.import.view',
                'processImport' => 'participants.import.process',
            ];

            $needed = $permissionMap[$action] ?? null;
            if ($needed && !$user->hasPermission($needed)) {
                abort(403, 'Access denied. Missing permission: ' . $needed);
            }

            return $next($request);
        });
    }

    /**
     * Download the sample CSV template
     * 
     * This provides users with a correctly formatted template including:
     * - All required and optional fields
     * - Sample data demonstrating valid values
     * - Field descriptions and constraints
     */
    public function downloadSample()
    {
        $filename = 'participants_bulk_upload_template.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                'first_name',
                'last_name',
                'email',
                'participant_type',
                'country',
                'gender',
                'pronoun',
                'contact_no',
                'date_of_birth',
                'field_of_work_study',
                'designation',
                'organization_institution',
                'address',
                'visa_status',
                'registration_status',
                'travel_intent',
                'arrival_date',
                'departure_date',
                'bio',
                'hashtags',
            ]);
            
            // Instructions row - will be automatically skipped during import
            fputcsv($file, [
                '⚠️ DELETE THIS ROW',
                '⚠️ DELETE THIS ROW',
                '⚠️ DELETE THIS ROW',
                '⚠️ DELETE THIS ROW',
                '⚠️ DELETE THIS ROW',
                'male|female|prefer_not_to_say',
                'he_him|she_her|they_them',
                'Phone format: +1234567890',
                '⚠️ USE: YYYY-MM-DD (e.g., 1990-01-15)',
                'Field of expertise',
                'Job title',
                'Organization name',
                'Full address',
                'required|not_required|pending|approved|issue',
                'pending|approved|rejected',
                'none|national|international',
                '⚠️ USE: YYYY-MM-DD HH:MM (e.g., 2024-06-10 14:00)',
                '⚠️ USE: YYYY-MM-DD HH:MM (e.g., 2024-06-15 10:00)',
                'Biography text',
                'tag1, tag2, tag3',
            ]);
            
            // Sample data rows - USE THESE AS EXAMPLES, then DELETE and add your data
            fputcsv($file, [
                'John',
                'Doe',
                'john.doe@example.com',
                'participant',
                'United States',
                'male',
                'he_him',
                '+1234567890',
                '1990-01-15',  // ← Use YYYY-MM-DD format!
                'Computer Science',
                'Software Engineer',
                'Tech Corp Inc',
                '123 Main St, New York, NY 10001',
                'required',
                'pending',
                'international',
                '2024-06-10 14:00',  // ← Use YYYY-MM-DD HH:MM format!
                '2024-06-15 10:00',
                'John is a software engineer with 10 years of experience',
                'technology, innovation, AI',
            ]);
            
            fputcsv($file, [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                'speaker',
                'United Kingdom',
                'female',
                'she_her',
                '+441234567890',
                '1985-05-20',  // ← Correct date format
                'Environmental Science',
                'Research Director',
                'Green Solutions Ltd',
                '456 Oak Avenue, London, UK',
                'not_required',
                'approved',
                'international',
                '2024-06-09 18:30',
                '2024-06-16 12:00',
                'Leading researcher in sustainable development',
                'environment, sustainability, research',
            ]);
            
            // Add note to user (in a format that will be skipped)
            fputcsv($file, []);
            fputcsv($file, ['=== DELETE ROWS 2-5 BEFORE UPLOADING ===']);
            fputcsv($file, ['=== Only keep row 1 (headers) and your actual data ===']);
            fputcsv($file, ['=== IMPORTANT: Dates must be YYYY-MM-DD format, participant_type must match your system types ===']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the bulk import form
     */
    public function showImportForm()
    {
        $conferences = Conference::orderBy('start_date', 'desc')->get();
        $participantTypes = ParticipantType::ordered()->get();
        
        return view('participants.import', compact('conferences', 'participantTypes'));
    }

    /**
     * Process the bulk import
     * 
     * Engineering Concepts Applied:
     * 1. ACID Transactions: Ensures atomicity - either all participants are imported or none
     * 2. Validation Layer: Separates validation logic from business logic
     * 3. Error Accumulation: Collects all errors instead of failing on first error
     * 4. Idempotency: Uses email as unique identifier to prevent duplicate imports
     * 
     * Why this approach?
     * - Data Integrity: Transactions prevent partial imports that could corrupt data
     * - User Experience: Detailed error messages help users fix issues quickly
     * - Performance: Batch processing is more efficient than individual API calls
     * - Security: Validation prevents malicious or malformed data
     */
    public function processImport(Request $request)
    {
        // Validate the request
        $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $conferenceId = $request->conference_id;
        $conference = Conference::findOrFail($conferenceId);
        
        // Read and parse CSV file
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom != pack('CCC', 0xEF, 0xBB, 0xBF)) {
            rewind($handle);
        }
        
        // Get headers
        $headers = fgetcsv($handle);
        
        if (!$headers) {
            return back()->with('error', 'Invalid CSV file. No headers found.');
        }
        
        // Normalize headers (trim and lowercase)
        $headers = array_map(function($header) {
            return strtolower(trim($header));
        }, $headers);
        
        // Remove empty headers (Excel sometimes adds extra columns)
        $headers = array_filter($headers, function($header) {
            return !empty($header);
        });
        
        // Reindex array after filtering
        $headers = array_values($headers);
        
        // Validate we have headers
        if (empty($headers)) {
            return back()->with('error', 'No valid headers found in CSV file. Please ensure the first row contains column names.');
        }
        
        // Validate required headers
        $requiredHeaders = ['first_name', 'last_name', 'email', 'participant_type'];
        $missingHeaders = array_diff($requiredHeaders, $headers);
        
        if (!empty($missingHeaders)) {
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missingHeaders));
        }
        
        $rows = [];
        $lineNumber = 2; // Start from 2 (after header row)
        
        // Read all rows
        while (($data = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($data))) {
                $lineNumber++;
                continue;
            }
            
            // Skip instruction rows (rows that contain warnings, instructions, etc.)
            if (isset($data[0])) {
                $firstCell = strtolower(trim($data[0]));
                if (
                    str_starts_with($firstCell, '===') ||
                    str_starts_with($firstCell, 'required') ||
                    str_starts_with($firstCell, '⚠️') ||
                    str_starts_with($firstCell, '1.') ||
                    str_starts_with($firstCell, '2.') ||
                    str_starts_with($firstCell, '3.') ||
                    str_starts_with($firstCell, '4.') ||
                    str_starts_with($firstCell, '5.') ||
                    str_starts_with($firstCell, '6.') ||
                    str_starts_with($firstCell, '7.') ||
                    str_starts_with($firstCell, '8.') ||
                    str_starts_with($firstCell, '9.') ||
                    str_starts_with($firstCell, '10.') ||
                    str_starts_with($firstCell, '11.') ||
                    str_starts_with($firstCell, '12.') ||
                    stripos($firstCell, 'delete this row') !== false ||
                    stripos($firstCell, 'instruction') !== false ||
                    stripos($firstCell, 'important') !== false ||
                    stripos($firstCell, 'note') !== false ||
                    stripos($firstCell, 'email:') !== false ||
                    stripos($firstCell, 'participant_type:') !== false ||
                    stripos($firstCell, 'common types:') !== false ||
                    stripos($firstCell, 'dates:') !== false ||
                    stripos($firstCell, 'datetime:') !== false ||
                    stripos($firstCell, 'travel_intent:') !== false ||
                    stripos($firstCell, 'gender:') !== false ||
                    stripos($firstCell, 'pronoun:') !== false ||
                    stripos($firstCell, 'visa_status:') !== false ||
                    stripos($firstCell, 'registration_status:') !== false ||
                    (isset($data[2]) && stripos($data[2], 'REQUIRED') !== false) ||
                    (isset($data[2]) && stripos($data[2], 'DELETE THIS ROW') !== false) ||
                    (isset($data[2]) && stripos($data[2], 'unique') !== false)
                ) {
                    $lineNumber++;
                    continue;
                }
            }
            
            // Skip if the email field looks like an instruction or is invalid
            if (isset($data[2])) {
                $email = trim($data[2]);
                if (empty($email) ||
                    stripos($email, 'REQUIRED') !== false ||
                    stripos($email, 'DELETE') !== false ||
                    stripos($email, '⚠️') !== false ||
                    stripos($email, 'unique') !== false ||
                    !filter_var($email, FILTER_VALIDATE_EMAIL)
                ) {
                    $lineNumber++;
                    continue;
                }
            }
            
            // Ensure data row has same number of columns as headers
            $headerCount = count($headers);
            $dataCount = count($data);
            
            if ($dataCount < $headerCount) {
                // Pad with empty strings if data has fewer columns
                $data = array_pad($data, $headerCount, '');
            } elseif ($dataCount > $headerCount) {
                // Trim extra columns if data has more columns
                $data = array_slice($data, 0, $headerCount);
            }
            
            // Create associative array with headers
            $row = array_combine($headers, $data);
            $row['line_number'] = $lineNumber;
            $rows[] = $row;
            $lineNumber++;
        }
        
        fclose($handle);
        
        if (empty($rows)) {
            return back()->with('error', 'No valid data rows found in the CSV file.');
        }
        
        // Process imports
        $successCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errors = [];
        $skipped = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($rows as $row) {
                try {
                    $result = $this->importParticipantRow($row, $conferenceId);
                    
                    if ($result['success']) {
                        $successCount++;
                    } elseif (isset($result['skipped']) && $result['skipped']) {
                        $skippedCount++;
                        $skipped[] = "Row {$row['line_number']}: " . $result['message'];
                    } else {
                        $errorCount++;
                        $errors[] = "Row {$row['line_number']}: " . $result['error'];
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row {$row['line_number']}: " . $e->getMessage();
                }
            }
            
            // If there are errors (not counting skipped duplicates), rollback and show errors
            if ($errorCount > 0) {
                DB::rollback();
                
                $errorMessage = "Import failed with $errorCount error(s):\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\n... and " . (count($errors) - 10) . " more errors.";
                }
                
                return back()->with('error', $errorMessage)->withInput();
            }
            
            DB::commit();
            
            // Build success message
            $message = "Successfully imported $successCount participant(s) for {$conference->name}.";
            if ($skippedCount > 0) {
                $message .= " ($skippedCount already registered and were skipped)";
            }
            
            return redirect()->route('participants.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Import failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Import a single participant row
     * 
     * This method handles the core business logic of creating/updating a participant:
     * 1. Validates the row data
     * 2. Finds or creates the user
     * 3. Creates the participant profile
     * 4. Creates travel details if applicable
     * 
     * Design Decision: Email as Primary Key
     * - Email is used to identify existing users
     * - Allows adding multiple conference profiles to same user
     * - Prevents duplicate user accounts
     * 
     * @param array $row The CSV row data
     * @param int $conferenceId The conference ID to associate with
     * @return array Success status and error message if any
     */
    private function importParticipantRow(array $row, int $conferenceId)
    {
        // Validate required fields
        if (empty($row['first_name']) || empty($row['last_name']) || empty($row['email']) || empty($row['participant_type'])) {
            return [
                'success' => false,
                'error' => 'Missing required fields (first_name, last_name, email, or participant_type)'
            ];
        }
        
        // Validate email format
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'error' => 'Invalid email format: ' . $row['email']
            ];
        }
        
        // Find participant type (case-insensitive)
        $participantType = ParticipantType::whereRaw('LOWER(name) = ?', [strtolower(trim($row['participant_type']))])->first();
        
        if (!$participantType) {
            return [
                'success' => false,
                'error' => 'Invalid participant type: ' . $row['participant_type']
            ];
        }
        
        // Validate enum fields if provided
        $validGenders = ['male', 'female', 'prefer_not_to_say'];
        if (!empty($row['gender']) && !in_array(strtolower($row['gender']), $validGenders)) {
            return [
                'success' => false,
                'error' => 'Invalid gender value. Must be one of: ' . implode(', ', $validGenders)
            ];
        }
        
        $validPronouns = ['he_him', 'she_her', 'they_them'];
        if (!empty($row['pronoun']) && !in_array(strtolower($row['pronoun']), $validPronouns)) {
            return [
                'success' => false,
                'error' => 'Invalid pronoun value. Must be one of: ' . implode(', ', $validPronouns)
            ];
        }
        
        $validVisaStatuses = ['required', 'not_required', 'pending', 'approved', 'issue'];
        if (!empty($row['visa_status']) && !in_array(strtolower($row['visa_status']), $validVisaStatuses)) {
            return [
                'success' => false,
                'error' => 'Invalid visa_status value. Must be one of: ' . implode(', ', $validVisaStatuses)
            ];
        }
        
        $validRegistrationStatuses = ['pending', 'approved', 'rejected'];
        if (!empty($row['registration_status']) && !in_array(strtolower($row['registration_status']), $validRegistrationStatuses)) {
            return [
                'success' => false,
                'error' => 'Invalid registration_status value. Must be one of: ' . implode(', ', $validRegistrationStatuses)
            ];
        }
        
        $validTravelIntents = ['none', 'national', 'international'];
        if (!empty($row['travel_intent']) && !in_array(strtolower($row['travel_intent']), $validTravelIntents)) {
            return [
                'success' => false,
                'error' => 'Invalid travel_intent value. Must be one of: ' . implode(', ', $validTravelIntents)
            ];
        }
        
        // Validate travel dates if travel intent is set
        $travelIntent = !empty($row['travel_intent']) ? strtolower($row['travel_intent']) : 'none';
        if (in_array($travelIntent, ['national', 'international'])) {
            if (empty($row['arrival_date']) || empty($row['departure_date'])) {
                return [
                    'success' => false,
                    'error' => 'arrival_date and departure_date are required when travel_intent is set to national or international'
                ];
            }
        }
        
        // Find or create user
        $user = User::where('email', $row['email'])->first();
        
        if (!$user) {
            // Create new user
            $password = Str::random(12);
            
            $user = User::create([
                'first_name' => trim($row['first_name']),
                'last_name' => trim($row['last_name']),
                'email' => strtolower(trim($row['email'])),
                'password' => bcrypt($password),
                'gender' => !empty($row['gender']) ? strtolower($row['gender']) : null,
                'pronoun' => !empty($row['pronoun']) ? strtolower($row['pronoun']) : null,
                'contact_no' => $row['contact_no'] ?? null,
                'date_of_birth' => $this->parseDateForDatabase($row['date_of_birth'] ?? null),
                'country' => $row['country'] ?? null,
                'field_of_work_study' => $row['field_of_work_study'] ?? null,
                'designation' => $row['designation'] ?? null,
                'organization_institution' => $row['organization_institution'] ?? null,
                'address' => $row['address'] ?? null,
            ]);
        }

        // No default role assignment during import
        
        // Check if participant already exists for this conference
        $existingParticipant = Participant::where('user_id', $user->id)
            ->where('conference_id', $conferenceId)
            ->first();
            
        if ($existingParticipant) {
            return [
                'success' => false,
                'skipped' => true,
                'message' => "Email {$row['email']} already registered for this conference (skipped)"
            ];
        }
        
        // Check if this is the first participant for the user
        $isFirstParticipant = $user->participants()->count() === 0;
        
        // Create participant
        $conference = Conference::find($conferenceId);
        $profileName = $user->first_name . ' ' . $user->last_name . ' - ' . $conference->name;
        
        $participant = Participant::create([
            'user_id' => $user->id,
            'conference_id' => $conferenceId,
            'participant_type_id' => $participantType->id,
            'visa_status' => !empty($row['visa_status']) ? strtolower($row['visa_status']) : 'pending',
            'registration_status' => !empty($row['registration_status']) ? strtolower($row['registration_status']) : 'pending',
            'travel_intent' => $travelIntent,
            'bio' => $row['bio'] ?? null,
            'hashtags' => $row['hashtags'] ?? null,
            'status' => 'active',
            'is_primary' => $isFirstParticipant,
            'profile_name' => $profileName,
            'profile_type' => 'personal',
        ]);
        
        
        // Create travel details if applicable
        if (in_array($travelIntent, ['national', 'international']) && !empty($row['arrival_date']) && !empty($row['departure_date'])) {
            TravelDetail::create([
                'participant_id' => $participant->id,
                'arrival_date' => $this->parseDateTimeForDatabase($row['arrival_date']),
                'departure_date' => $this->parseDateTimeForDatabase($row['departure_date']),
            ]);
        }
        
        return ['success' => true];
    }
    
    /**
     * Parse datetime string for database storage
     * 
     * Accepts formats like:
     * - YYYY-MM-DD HH:MM
     * - YYYY-MM-DD HH:MM:SS
     * - YYYY-MM-DDTHH:MM
     * - M/D/YYYY HH:MM (Excel format)
     * - MM/DD/YYYY HH:MM (Excel format)
     * 
     * @param string $dateTimeString
     * @return string|null
     */
    private function parseDateTimeForDatabase($dateTimeString)
    {
        if (empty($dateTimeString)) {
            return null;
        }
        
        // Replace T with space if present
        $dateTimeString = str_replace('T', ' ', trim($dateTimeString));
        
        // Try to parse the datetime
        try {
            $dt = new \DateTime($dateTimeString);
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Parse date string for database storage
     * 
     * Handles multiple date formats including Excel's default format
     * 
     * @param string $dateString
     * @return string|null
     */
    private function parseDateForDatabase($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        $dateString = trim($dateString);
        
        // Try common formats
        $formats = [
            'Y-m-d',           // 1990-01-15 (correct format)
            'm/d/Y',           // 1/15/1990 (Excel format)
            'm-d-Y',           // 1-15-1990
            'd/m/Y',           // 15/1/1990
            'd-m-Y',           // 15-1-1990
            'Y/m/d',           // 1990/1/15
            'm/d/y',           // 1/15/90
            'd/m/y',           // 15/1/90
        ];
        
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $dateString);
            if ($dt !== false && $dt->format($format) === $dateString) {
                return $dt->format('Y-m-d');
            }
        }
        
        // Try general parsing as last resort
        try {
            $dt = new \DateTime($dateString);
            return $dt->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}


