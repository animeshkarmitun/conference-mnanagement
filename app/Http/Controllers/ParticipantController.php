<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\ParticipantType;
use App\Models\User;
use App\Models\TravelDetail;
use App\Models\Hotel;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ParticipantController extends Controller
{
    // List all participants
    public function index(Request $request)
    {
        $query = Participant::with(['user', 'conference', 'participantType']);
        
        // Status filtering
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('registration_status', $request->status);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('organization', 'like', "%{$search}%");
                })
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('organization', 'like', "%{$search}%");
            });
        }
        
        // Visa status filtering
        if ($request->has('visa_filter')) {
            $query->where('visa_status', $request->visa_filter);
        }
        
        // Type filtering (keep existing functionality)
        if ($request->has('type')) {
            $query->whereHas('participantType', function($q) use ($request) {
                $q->where('name', $request->type);
            });
        }
        
        // Get counts for tabs
        $counts = [
            'approved' => Participant::where('registration_status', 'approved')->count(),
            'pending' => Participant::where('registration_status', 'pending')->count(),
            'rejected' => Participant::where('registration_status', 'rejected')->count(),
            'all' => Participant::count(),
        ];
        
        // Get visa status counts
        $visaCounts = [
            'required' => Participant::where('visa_status', 'required')->count(),
            'approved' => Participant::where('visa_status', 'approved')->count(),
            'pending' => Participant::where('visa_status', 'pending')->count(),
            'issue' => Participant::where('visa_status', 'issue')->count(),
            'not_required' => Participant::where('visa_status', 'not_required')->count(),
        ];
        
        // Get participant type counts
        $typeCounts = [];
        $participantTypes = ParticipantType::all();
        foreach ($participantTypes as $type) {
            $typeCounts[$type->name] = Participant::where('participant_type_id', $type->id)->count();
        }
        
        // Handle CSV export
        if ($request->has('export') && $request->export === 'csv') {
            $participants = $query->latest()->get();
            
            $filename = 'participants_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($participants) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Serial Number', 'First Name', 'Last Name', 'Email', 'Gender', 
                    'Nationality', 'Profession', 'Age', 'Participant Type', 'Category',
                    'Organization', 'Registration Status', 'Visa Status', 'Conference'
                ]);
                
                // CSV data
                foreach ($participants as $participant) {
                    $user = $participant->user;
                    $dob = $user->date_of_birth ?? null;
                    $age = $dob ? \Carbon\Carbon::parse($dob)->age : '';
                    
                    fputcsv($file, [
                        $participant->serial_number ?? '',
                        $user->first_name ?? '',
                        $user->last_name ?? '',
                        $user->email ?? '',
                        $user->gender ?? '',
                        $user->nationality ?? '',
                        $user->profession ?? '',
                        $age,
                        $participant->participantType->name ?? '',
                        $participant->category ?? '',
                        $participant->organization ?? '',
                        $participant->registration_status ?? '',
                        $participant->visa_status ?? '',
                        $participant->conference->name ?? '',
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        $participants = $query->latest()->paginate(20);
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        
        return view('participants.index', compact('participants', 'counts', 'visaCounts', 'typeCounts', 'participantTypes', 'status', 'search'));
    }

    // Show create form
    public function create()
    {
        $conferences = Conference::all();
        $participantTypes = ParticipantType::all();
        return view('participants.create', compact('conferences', 'participantTypes'));
    }

    // Store new participant
    public function store(Request $request)
    {
        // Validate user creation data
        $userValidated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'organization' => 'nullable|string|max:255',
            'dietary_needs' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Validate participant data
        $participantValidated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'participant_type_id' => 'required|exists:participant_types,id',
            'visa_status' => 'required|in:required,not_required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
            'travel_form_submitted' => 'boolean',
            'bio' => 'nullable|string',
            'approved' => 'boolean',
            'travel_intent' => 'required',
            'registration_status' => 'required',
            'category' => 'nullable|string|max:50',
        ]);

        // Create the user first
        $user = User::create([
            'first_name' => $userValidated['first_name'],
            'last_name' => $userValidated['last_name'],
            'email' => $userValidated['email'],
            'password' => bcrypt($userValidated['password']),
            'gender' => $userValidated['gender'],
            'nationality' => $userValidated['nationality'],
            'profession' => $userValidated['profession'],
            'date_of_birth' => $userValidated['date_of_birth'],
            'organization' => $userValidated['organization'],
            'dietary_needs' => $userValidated['dietary_needs'],
        ]);

        // Handle file uploads for the user
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $profilePicturePath;
        }
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $user->resume = $resumePath;
        }
        $user->save();

        // If visa status is not 'issue', clear the description
        if ($participantValidated['visa_status'] !== 'issue') {
            $participantValidated['visa_issue_description'] = null;
        }

        // Generate serial number
        $year = date('Y');
        $lastParticipant = Participant::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $lastParticipant ? intval(substr($lastParticipant->serial_number, -3)) + 1 : 1;
        $serialNumber = "CONF{$year}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        // Add user_id and serial_number to participant data
        $participantValidated['user_id'] = $user->id;
        $participantValidated['serial_number'] = $serialNumber;
        $participantValidated['travel_intent'] = $request->travel_intent == '1' ? true : false;

        // Create the participant
        Participant::create($participantValidated);
        
        return redirect()->route('participants.index')->with('success', 'Participant created successfully.');
    }

    // Show participant details
    public function show(Participant $participant)
    {
        $participant->load(['user', 'conference', 'participantType']);
        $sessions = $participant->sessions()->withPivot('role')->get();
        $notifications = $participant->user->notifications()->latest()->get();
        $comments = $participant->comments()->with('user')->latest()->get();
        $travelDetail = $participant->travelDetails;
        $hotels = Hotel::all();
        return view('participants.show', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels'));
    }

    // Show edit form
    public function edit(Participant $participant)
    {
        $conferences = Conference::all();
        $participantTypes = ParticipantType::all();
        $users = User::all();
        return view('participants.edit', compact('participant', 'conferences', 'participantTypes', 'users'));
    }

    // Update participant
    public function update(Request $request, Participant $participant)
    {
        // Validate participant data
        $participantValidated = $request->validate([
            'visa_status' => 'required|in:required,not_required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:500',
            'organization' => 'nullable|string|max:100',
            'dietary_needs' => 'nullable|string|max:50',
            'dietary_needs_other' => 'nullable|string|max:100',
        ]);

        // Validate user data
        $userValidated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . $participant->user_id,
        ]);

        // Validate file uploads
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Update user data
        $user = $participant->user;
        $user->update([
            'first_name' => $userValidated['first_name'],
            'last_name' => $userValidated['last_name'],
            'email' => $userValidated['email'],
        ]);

        // Handle file uploads
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $profilePicturePath;
            $user->save();
        }

        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($user->resume) {
                Storage::disk('public')->delete($user->resume);
            }
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $user->resume = $resumePath;
            $user->save();
        }

        // Handle dietary needs
        if ($participantValidated['dietary_needs'] === 'other' && $request->filled('dietary_needs_other')) {
            $participantValidated['dietary_needs'] = $request->dietary_needs_other;
        }

        // If visa status is not 'issue', clear the description
        if ($participantValidated['visa_status'] !== 'issue') {
            $participantValidated['visa_issue_description'] = null;
        }

        // Update participant data
        $participant->update($participantValidated);
        
        // Redirect based on who is updating (admin vs participant)
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin')) {
            return redirect()->route('participants.index')->with('success', 'Participant updated successfully.');
        } else {
            return redirect()->back()->with('success', 'Profile updated successfully.');
        }
    }

    // Delete participant
    public function destroy(Participant $participant)
    {
        $participant->delete();
        return redirect()->route('participants.index')->with('success', 'Participant deleted successfully.');
    }

    // Profile dashboard for logged-in participant
    public function profile()
    {
        $participant = Participant::with(['conference', 'participantType', 'sessions'])
            ->where('user_id', Auth::id())
            ->latest()->first();
        
        if (!$participant) {
            return redirect()->back()->with('error', 'No profile found for your account.');
        }
        
        $sessions = $participant->sessions()->withPivot('role')->get();
        \Log::info('Sessions loaded for participant ' . $participant->id . ': ' . $sessions->count());
        $notifications = $participant->user->notifications()->latest()->get();
        $comments = $participant->comments()->with('user')->latest()->get();
        $travelDetail = $participant->travelDetails;
        $hotels = Hotel::all();
        
        return view('participants.show', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels'));
    }

    public function updateTravel(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'flight_info' => 'nullable|string',
            'hotel_id' => 'nullable|exists:hotels,id',
            'extra_nights' => 'nullable|integer|min:0',
            'travel_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $travelDetail = $participant->travelDetails ?: $participant->travelDetails()->make();

        $travelDetail->arrival_date = $validated['arrival_date'] ?? null;
        $travelDetail->departure_date = $validated['departure_date'] ?? null;
        $travelDetail->flight_info = $validated['flight_info'] ?? null;
        $travelDetail->hotel_id = $validated['hotel_id'] ?? null;
        $travelDetail->extra_nights = $validated['extra_nights'] ?? 0;

        if ($request->hasFile('travel_documents')) {
            $travelDocumentPath = $request->file('travel_documents')->store('travel_documents', 'public');
            $travelDetail->travel_documents = $travelDocumentPath;
        }

        $travelDetail->participant_id = $participant->id;
        $travelDetail->save();

        return redirect()->back()->with('success', 'Travel details updated successfully.');
    }

    /**
     * Download participant biographies as PDF or ZIP
     */
    public function downloadBiographies(Request $request)
    {
        // Check if user is admin (simple check for now)
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
        }

        // Validate request
        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'format' => 'required|in:pdf,zip'
        ]);

        // Get selected participants with their user data
        $participants = Participant::with(['user', 'participantType'])
            ->whereIn('id', $validated['participant_ids'])
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->back()->with('error', 'No valid participants selected.');
        }

        // Filter participants who have biographies
        $participantsWithBios = $participants->filter(function ($participant) {
            return !empty(trim($participant->bio));
        });

        if ($participantsWithBios->isEmpty()) {
            return redirect()->back()->with('error', 'None of the selected participants have biographies.');
        }

        // Generate filename
        $filename = 'participant_biographies_' . date('Y-m-d_H-i-s');

        if ($validated['format'] === 'pdf') {
            // Generate PDF
            $pdf = PDF::loadView('participants.biographies-pdf', [
                'participants' => $participantsWithBios
            ]);
            
            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            
            return $pdf->download($filename . '.pdf');
        } else {
            // For ZIP format, return text file for now (will be implemented in Phase 3)
            $content = "PARTICIPANT BIOGRAPHIES\n";
            $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
            $content .= "Total participants: " . $participantsWithBios->count() . "\n\n";
            $content .= str_repeat("=", 50) . "\n\n";

            foreach ($participantsWithBios as $participant) {
                $content .= "NAME: " . ($participant->user->first_name ?? $participant->user->name) . " " . ($participant->user->last_name ?? '') . "\n";
                $content .= "EMAIL: " . $participant->user->email . "\n";
                $content .= "ORGANIZATION: " . ($participant->organization ?? 'N/A') . "\n";
                $content .= "TYPE: " . ($participant->participantType->name ?? 'N/A') . "\n";
                $content .= "BIOGRAPHY:\n" . $participant->bio . "\n";
                $content .= str_repeat("-", 30) . "\n\n";
            }

            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.txt"');
        }
    }

    /**
     * Download participant resume
     */
    public function downloadResume(Participant $participant)
    {
        // Check if user has permission to download this resume
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super_admin') && Auth::id() !== $participant->user_id) {
            abort(403, 'Unauthorized access');
        }

        // Check if resume exists
        if (!$participant->user->resume || !Storage::disk('public')->exists($participant->user->resume)) {
            abort(404, 'Resume not found');
        }

        // Get file path and name
        $filePath = Storage::disk('public')->path($participant->user->resume);
        $fileName = basename($participant->user->resume);

        // Return file download response
        return response()->download($filePath, $fileName);
    }

    /**
     * Display profile picture
     */
    public function showProfilePicture(Participant $participant)
    {
        // Check if user has permission to view this profile picture
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super_admin') && Auth::id() !== $participant->user_id) {
            abort(403, 'Unauthorized access');
        }

        // Check if profile picture exists
        if (!$participant->user->profile_picture || !Storage::disk('public')->exists($participant->user->profile_picture)) {
            abort(404, 'Profile picture not found');
        }

        // Return the image file
        $filePath = Storage::disk('public')->path($participant->user->profile_picture);
        return response()->file($filePath);
    }

    /**
     * Assign session to participant
     */
    public function assignSession(Request $request, Participant $participant)
    {
        \Log::info('Session assignment attempt - User: ' . Auth::id() . ', Participant: ' . $participant->id);
        
        // Check permissions - allow if user is admin, super_admin, or is assigning to themselves
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles) || $user->id === $participant->user_id;
        
        if (!$hasPermission) {
            \Log::warning('Permission denied for session assignment - User: ' . $user->id . ', Roles: ' . $user->roles->pluck('name')->implode(', '));
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        \Log::info('Session assignment request data: ' . json_encode($request->all()));
        
        $validated = $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'role' => 'required|in:participant,speaker,moderator,panelist,organizer',
        ]);

        // Check if session belongs to the same conference
        $session = Session::find($validated['session_id']);
        if ($session->conference_id !== $participant->conference_id) {
            return redirect()->back()->with('error', 'Session does not belong to the same conference');
        }

        // Check if participant is already assigned to this session
        if ($participant->sessions()->where('session_id', $validated['session_id'])->exists()) {
            return redirect()->back()->with('error', 'Participant is already assigned to this session');
        }

        try {
            // Assign session to participant
            $participant->sessions()->attach($validated['session_id'], [
                'role' => $validated['role'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            \Log::info('Session assigned: Participant ' . $participant->id . ' assigned to Session ' . $validated['session_id'] . ' with role ' . $validated['role']);

            // Verify the assignment was successful
            $assignedSession = $participant->sessions()->where('session_id', $validated['session_id'])->first();
            if (!$assignedSession) {
                return redirect()->back()->with('error', 'Failed to assign session. Please try again.');
            }

            return redirect()->back()->with('success', 'Session assigned successfully');
        } catch (\Exception $e) {
            \Log::error('Session assignment failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign session: ' . $e->getMessage());
        }
    }

    /**
     * Remove session from participant
     */
    public function removeSession(Request $request, Participant $participant)
    {
        // Check permissions
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super_admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:sessions,id',
        ]);

        // Remove session from participant
        $participant->sessions()->detach($validated['session_id']);

        return response()->json(['success' => true, 'message' => 'Session removed successfully']);
    }

    /**
     * Update participant status
     */
    public function updateStatus(Request $request, Participant $participant)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validated = $request->validate([
            'registration_status' => 'required|in:pending,approved,rejected',
            'approved' => 'required|boolean',
        ]);

        try {
            $participant->update([
                'registration_status' => $validated['registration_status'],
                'approved' => $validated['approved'],
            ]);

            $statusText = ucfirst($validated['registration_status']);
            return response()->json([
                'success' => true, 
                'message' => "Participant status updated to {$statusText} successfully"
            ]);
        } catch (\Exception $e) {
            \Log::error('Status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update participant status
     */
    public function bulkUpdate(Request $request)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $updatedCount = Participant::whereIn('id', $validated['participant_ids'])
                ->update([
                    'registration_status' => $validated['status'],
                    'approved' => $validated['status'] === 'approved',
                ]);

            $statusText = ucfirst($validated['status']);
            return redirect()->back()->with('success', "Successfully updated {$updatedCount} participant(s) to {$statusText} status");
        } catch (\Exception $e) {
            \Log::error('Bulk update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update participants: ' . $e->getMessage());
        }
    }
} 