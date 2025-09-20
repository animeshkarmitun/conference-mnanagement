<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\ParticipantType;
use App\Models\User;
use App\Models\TravelDetail;
use App\Models\Hotel;
use App\Models\Session;
use App\Services\TravelNotificationService;
use App\Services\ProfileNotificationService;
use App\Events\SessionEvent;
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

        // Optional conference filter
        $conferenceId = $request->get('conference_id');
        if (!empty($conferenceId) && \App\Models\Conference::where('id', $conferenceId)->exists()) {
            $query->where('conference_id', $conferenceId);
        } else {
            $conferenceId = null; // normalize invalid id to null
        }
        
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
        
        // Get counts for tabs (scoped only by conference filter)
        $countsScope = Participant::query();
        if ($conferenceId) {
            $countsScope->where('conference_id', $conferenceId);
        }
        $counts = [
            'approved' => (clone $countsScope)->where('registration_status', 'approved')->count(),
            'pending' => (clone $countsScope)->where('registration_status', 'pending')->count(),
            'rejected' => (clone $countsScope)->where('registration_status', 'rejected')->count(),
            'all' => (clone $countsScope)->count(),
        ];
        
        // Get visa status counts (scoped by conference)
        $visaCounts = [
            'required' => (clone $countsScope)->where('visa_status', 'required')->count(),
            'approved' => (clone $countsScope)->where('visa_status', 'approved')->count(),
            'pending' => (clone $countsScope)->where('visa_status', 'pending')->count(),
            'issue' => (clone $countsScope)->where('visa_status', 'issue')->count(),
            'not_required' => (clone $countsScope)->where('visa_status', 'not_required')->count(),
        ];
        
        // Get participant type counts
        $typeCounts = [];
        $participantTypes = ParticipantType::all();
        foreach ($participantTypes as $type) {
            $typeCounts[$type->name] = (clone $countsScope)->where('participant_type_id', $type->id)->count();
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
        
        $participants = $query->latest()->paginate(20)->withQueryString();
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        // Conferences list for filter (adjust for permissions if needed)
        $conferences = Conference::orderBy('start_date', 'desc')->get();

        return view('participants.index', compact('participants', 'counts', 'visaCounts', 'typeCounts', 'participantTypes', 'status', 'search', 'conferences', 'conferenceId'));
    }

    // Show create form
    public function create()
    {
        $conferences = Conference::all();
        $participantTypes = ParticipantType::ordered()->get();
        
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
        // Preload available sessions for modal (same conference if available)
        $availableSessions = collect();
        if ($participant->conference_id) {
            $availableSessions = \App\Models\Session::where('conference_id', $participant->conference_id)
                ->orderBy('start_time','asc')
                ->get(['id','title','start_time','end_time','venue_id']);
        }
        $notifications = $participant->user->notifications()->latest()->get();
        $comments = $participant->comments()->with('user')->latest()->get();
        $travelDetail = $participant->travelDetails;
        $hotels = Hotel::all();
        
        // Determine if the current user is an admin/superadmin viewing someone else's profile
        $isAdminViewing = (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) && 
                         auth()->id() !== $participant->user_id;
        
        if ($isAdminViewing) {
            // Use admin layout for admin viewing participant details
            return view('participants.show-admin', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels', 'availableSessions'));
        } else {
            // Use participant layout for participants viewing their own profile
            return view('participants.show', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels', 'availableSessions'));
        }
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
        // Check if this is a personal info update (participant updating their own profile)
        $isPersonalUpdate = !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin');
        
        if ($isPersonalUpdate) {
            // Personal info update - only validate personal fields
            $participantValidated = $request->validate([
                'visa_status' => 'nullable|in:required,not_required,pending,approved,issue',
                'visa_issue_description' => 'nullable|string|max:1000',
                'bio' => 'nullable|string|max:500',
                'organization' => 'nullable|string|max:100',
                'dietary_needs' => 'nullable|string|max:50',
                'dietary_needs_other' => 'nullable|string|max:100',
                'travel_form_submitted' => 'boolean',
                'travel_intent' => 'boolean',
            ]);

            // Validate user data for personal update
            $userValidated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:255|unique:users,email,' . $participant->user_id,
            ]);
        } else {
            // Admin update - validate all fields
            $participantValidated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'conference_id' => 'required|exists:conferences,id',
                'participant_type_id' => 'required|exists:participant_types,id',
                'visa_status' => 'required|in:required,not_required,pending,approved,issue',
                'visa_issue_description' => 'nullable|string|max:1000',
                'bio' => 'nullable|string|max:500',
                'organization' => 'nullable|string|max:100',
                'dietary_needs' => 'nullable|string|max:50',
                'dietary_needs_other' => 'nullable|string|max:100',
                'travel_form_submitted' => 'boolean',
                'approved' => 'boolean',
                'travel_intent' => 'boolean',
                'registration_status' => 'required|in:pending,approved,rejected',
            ]);

            // Validate user data for admin update
            $userValidated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:255|unique:users,email,' . $participant->user_id,
            ]);
        }

        // Validate file uploads
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Store old data for comparison
        $oldUserData = [
            'first_name' => $participant->user->first_name,
            'last_name' => $participant->user->last_name,
            'email' => $participant->user->email,
        ];
        $oldParticipantData = [
            'visa_status' => $participant->visa_status,
            'dietary_needs' => $participant->dietary_needs,
            'organization' => $participant->organization,
            'conference_id' => $participant->conference_id,
        ];

        // Update user data
        if ($isPersonalUpdate) {
            // Personal update - always update the current participant's user
            $user = $participant->user;
            $user->update([
                'first_name' => $userValidated['first_name'],
                'last_name' => $userValidated['last_name'],
                'email' => $userValidated['email'],
            ]);
        } else {
            // Admin update - handle user_id changes
            if ($participant->user_id == $participantValidated['user_id']) {
                $user = $participant->user;
                $user->update([
                    'first_name' => $userValidated['first_name'],
                    'last_name' => $userValidated['last_name'],
                    'email' => $userValidated['email'],
                ]);
            } else {
                // If user_id changed, get the new user and update their data
                $user = User::find($participantValidated['user_id']);
                if ($user) {
                    $user->update([
                        'first_name' => $userValidated['first_name'],
                        'last_name' => $userValidated['last_name'],
                        'email' => $userValidated['email'],
                    ]);
                }
            }
        }

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

        // Handle checkbox fields (convert to boolean)
        $participantValidated['travel_form_submitted'] = $request->has('travel_form_submitted');
        $participantValidated['travel_intent'] = $request->has('travel_intent');
        
        // Only handle 'approved' for admin updates
        if (!$isPersonalUpdate) {
            $participantValidated['approved'] = $request->has('approved');
        }

        // Check if conference has changed and handle conference-specific data cleanup
        if (!$isPersonalUpdate && isset($participantValidated['conference_id']) && 
            $oldParticipantData['conference_id'] != $participantValidated['conference_id']) {
            $this->handleConferenceChange($participant, $oldParticipantData['conference_id'], $participantValidated['conference_id']);
        }

        // Update participant data
        $participant->update($participantValidated);
        
        // Send notifications for both admin and participant updates
        $profileNotificationService = new ProfileNotificationService();
        $changes = [];
        
        // Check for personal info changes
        $newUserData = [
            'first_name' => $userValidated['first_name'],
            'last_name' => $userValidated['last_name'],
            'email' => $userValidated['email'],
        ];
        
        if (($oldUserData['first_name'] ?? '') !== ($newUserData['first_name'] ?? '') ||
            ($oldUserData['last_name'] ?? '') !== ($newUserData['last_name'] ?? '') ||
            ($oldUserData['email'] ?? '') !== ($newUserData['email'] ?? '')) {
            $changes['personal_info'] = true;
            $profileNotificationService->notifyPersonalInfoUpdated($participant, $oldUserData, $newUserData);
        }
        
        // Check for visa status changes
        if ($oldParticipantData['visa_status'] !== $participantValidated['visa_status']) {
            $changes['visa_status'] = true;
            $profileNotificationService->notifyVisaStatusUpdated($participant, $oldParticipantData['visa_status'], $participantValidated['visa_status']);
        }
        
        // Check for dietary needs changes
        if (($oldParticipantData['dietary_needs'] ?? '') !== ($participantValidated['dietary_needs'] ?? '')) {
            $changes['dietary_needs'] = true;
            $profileNotificationService->notifyDietaryNeedsUpdated($participant, $oldParticipantData['dietary_needs'] ?? '', $participantValidated['dietary_needs'] ?? '');
        }
        
        // Check for organization changes
        if (($oldParticipantData['organization'] ?? '') !== ($participantValidated['organization'] ?? '')) {
            $changes['organization'] = true;
            $profileNotificationService->notifyOrganizationUpdated($participant, $oldParticipantData['organization'] ?? '', $participantValidated['organization'] ?? '');
        }
        
        // Check for document uploads
        if ($request->hasFile('profile_picture')) {
            $changes['profile_picture'] = true;
            $profileNotificationService->notifyDocumentUploaded($participant, 'Profile Picture');
        }
        
        if ($request->hasFile('resume')) {
            $changes['resume'] = true;
            $profileNotificationService->notifyDocumentUploaded($participant, 'Resume');
        }
        
        // Send email notification to participant if admin made changes
        if ((Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin')) && !empty($changes)) {
            $this->sendParticipantUpdateEmail($participant, $changes, Auth::user());
        }
        
        // Redirect based on who is updating (admin vs participant)
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin')) {
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

        $hasDocuments = $request->hasFile('travel_documents');
        if ($hasDocuments) {
            $travelDocumentPath = $request->file('travel_documents')->store('travel_documents', 'public');
            $travelDetail->travel_documents = $travelDocumentPath;
        }

        $travelDetail->participant_id = $participant->id;
        $travelDetail->save();

        // Send travel notifications
        $travelNotificationService = new TravelNotificationService();
        
        // Notify about travel details update
        $travelNotificationService->notifyTravelDetailsUpdated($participant, $travelDetail);
        
        // If travel documents were uploaded, send additional notification
        if ($hasDocuments) {
            $travelNotificationService->notifyTravelDocumentsUploaded($participant);
        }

        return redirect()->back()->with('success', 'Travel details updated successfully.');
    }

    /**
     * Download participant biographies as PDF or ZIP
     */
    public function downloadBiographies(Request $request)
    {
        // Check permissions - allow admin, super_admin, and superadmin roles
        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Access denied. Please log in.');
        }
        
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || 
                        in_array('super_admin', $userRoles) || 
                        in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
        }

        // Validate request
        $validated = $request->validate([
            'participant_ids' => 'array',
            'participant_ids.*' => 'exists:participants,id',
            'format' => 'required|in:pdf,zip'
        ]);

        // Get participants - if no IDs provided, get all participants
        $query = Participant::with(['user', 'participantType']);
        if (!empty($validated['participant_ids'])) {
            $query->whereIn('id', $validated['participant_ids']);
        }
        $participants = $query->get();

        if ($participants->isEmpty()) {
            return redirect()->back()->with('error', 'No participants found.');
        }

        // Filter participants who have resumes
        $participantsWithResumes = $participants->filter(function ($participant) {
            return $participant->user->resume && Storage::disk('public')->exists($participant->user->resume);
        });

        if ($participantsWithResumes->isEmpty()) {
            return redirect()->back()->with('error', 'None of the selected participants have resume files uploaded.');
        }

        // Generate filename
        $filename = 'participant_resumes_' . date('Y-m-d_H-i-s');

        // Create ZIP file with resume files
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/temp/' . $filename . '.zip');
        
        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }
        
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Could not create ZIP file.');
        }
        
        foreach ($participantsWithResumes as $participant) {
            $resumePath = Storage::disk('public')->path($participant->user->resume);
            $originalFileName = basename($participant->user->resume);
            $participantName = ($participant->user->first_name ?? '') . '_' . ($participant->user->last_name ?? '');
            $participantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $participantName); // Sanitize filename
            $newFileName = $participantName . '_resume.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
            
            $zip->addFile($resumePath, $newFileName);
        }
        
        $zip->close();
        
        return response()->download($zipPath)->deleteFileAfterSend();
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
        
        // Support bulk assignment: session_ids[] and roles map
        $isBulk = $request->has('session_ids');
        if ($isBulk) {
            $data = $request->validate([
                'session_ids' => 'required|array',
                'session_ids.*' => 'exists:sessions,id',
                'roles' => 'nullable|array',
            ]);

            $sessionIds = $data['session_ids'];
            $roles = $data['roles'] ?? [];

            foreach ($sessionIds as $sid) {
                $session = Session::find($sid);
                if (!$session || $session->conference_id !== $participant->conference_id) {
                    return response()->json(['success' => false, 'message' => 'Session must belong to participant\'s conference'], 422);
                }
            }

            foreach ($sessionIds as $sid) {
                if (!$participant->sessions()->where('session_id', $sid)->exists()) {
                    $participant->sessions()->attach($sid, [
                        'role' => $roles[$sid] ?? 'participant',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Trigger session assignment event
                    $session = Session::find($sid);
                    if ($session) {
                        $message = "You have been assigned to the session '{$session->title}' as " . ($roles[$sid] ?? 'participant');
                        event(new SessionEvent($session, 'session_assigned', $message, [
                            'participant_id' => $participant->id,
                            'role' => $roles[$sid] ?? 'participant',
                            'assigned_by' => Auth::user()->id
                        ]));
                    }
                }
            }

            return response()->json(['success' => true]);
        }

        // Fallback: single assignment (legacy form)
        $validated = $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'role' => 'required|in:participant,speaker,moderator,panelist,organizer',
        ]);

        $session = Session::find($validated['session_id']);
        if ($session->conference_id !== $participant->conference_id) {
            return redirect()->back()->with('error', 'Session does not belong to the same conference');
        }
        if ($participant->sessions()->where('session_id', $validated['session_id'])->exists()) {
            return redirect()->back()->with('error', 'Participant is already assigned to this session');
        }

        $participant->sessions()->attach($validated['session_id'], [
            'role' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Trigger session assignment event
        $message = "You have been assigned to the session '{$session->title}' as {$validated['role']}";
        event(new SessionEvent($session, 'session_assigned', $message, [
            'participant_id' => $participant->id,
            'role' => $validated['role'],
            'assigned_by' => Auth::user()->id
        ]));
        
        return redirect()->back()->with('success', 'Session assigned successfully');
    }

    /**
     * Remove session from participant
     */
    public function removeSession(Request $request, Participant $participant)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:sessions,id',
        ]);

        // Get session details before removing
        $session = Session::find($validated['session_id']);
        
        // Verify session belongs to the same conference as participant
        if ($session && $session->conference_id !== $participant->conference_id) {
            return response()->json(['success' => false, 'message' => 'Session does not belong to the same conference'], 422);
        }
        
        // Check if participant is actually assigned to this session
        if (!$participant->sessions()->where('session_id', $validated['session_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Participant is not assigned to this session'], 422);
        }
        
        // Remove session from participant
        $participant->sessions()->detach($validated['session_id']);
        
        // Trigger session removal event
        if ($session) {
            $message = "Your assignment to the session '{$session->title}' has been removed";
            event(new SessionEvent($session, 'session_removed', $message, [
                'participant_id' => $participant->id,
                'removed_by' => Auth::user()->id
            ]));
        }

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

    /**
     * Send email notification to participant when admin updates their information
     */
    private function sendParticipantUpdateEmail(Participant $participant, array $changes, User $adminUser)
    {
        try {
            $changeDescriptions = [];
            
            if (isset($changes['personal_info'])) {
                $changeDescriptions[] = 'personal information';
            }
            if (isset($changes['visa_status'])) {
                $changeDescriptions[] = 'visa status';
            }
            if (isset($changes['dietary_needs'])) {
                $changeDescriptions[] = 'dietary preferences';
            }
            if (isset($changes['organization'])) {
                $changeDescriptions[] = 'organization details';
            }
            if (isset($changes['profile_picture'])) {
                $changeDescriptions[] = 'profile picture';
            }
            if (isset($changes['resume'])) {
                $changeDescriptions[] = 'resume';
            }
            
            $changesText = implode(', ', $changeDescriptions);
            $subject = "Your Profile Has Been Updated - {$participant->conference->name}";
            
            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Profile Update Notification</h2>
                    
                    <p>Dear {$participant->user->first_name} {$participant->user->last_name},</p>
                    
                    <p>Your profile information for the <strong>{$participant->conference->name}</strong> conference has been updated by our administrative team.</p>
                    
                    <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #374151; margin-top: 0;'>Updated Information:</h3>
                        <ul style='color: #4b5563;'>
            ";
            
            foreach ($changeDescriptions as $change) {
                $emailBody .= "<li style='margin-bottom: 5px;'>" . ucfirst($change) . "</li>";
            }
            
            $emailBody .= "
                        </ul>
                    </div>
                    
                    <p>Please log in to your account to review the changes and ensure all information is correct.</p>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('participants.show', $participant) . "' 
                           style='background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Your Profile
                        </a>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        If you have any questions or concerns about these changes, please contact our support team.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
            ";
            
            // Use EmailTrackingService to send the email
            $emailTrackingService = app(\App\Services\EmailTrackingService::class);
            
            $emailTrackingService->sendTrackedEmailViaGmail(
                $participant->user->email,
                $subject,
                $emailBody,
                'profile_update',
                $adminUser,
                $participant->conference,
                'Participant',
                $participant->id,
                'participant_update_notification'
            );
            
            \Log::info('Participant update email sent', [
                'participant_id' => $participant->id,
                'admin_user_id' => $adminUser->id,
                'changes' => $changes
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send participant update email', [
                'participant_id' => $participant->id,
                'admin_user_id' => $adminUser->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email to participant
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'conference_id' => 'nullable|exists:conferences,id'
        ]);

        try {
            $emailTrackingService = app(\App\Services\EmailTrackingService::class);
            $conference = $request->conference_id ? \App\Models\Conference::find($request->conference_id) : null;
            
            $email = $emailTrackingService->sendTrackedEmailViaGmail(
                $request->to,
                $request->subject,
                $request->message,
                \App\Models\Email::TYPE_GENERAL,
                auth()->user(),
                $conference,
                'Participant',
                null,
                'manual_email'
            );

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully!',
                'email_id' => $email->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send email to participant', [
                'to' => $request->to,
                'subject' => $request->subject,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle conference change for participant - cleanup conference-specific data
     */
    private function handleConferenceChange(Participant $participant, $oldConferenceId, $newConferenceId)
    {
        try {
            \Log::info('Handling conference change for participant', [
                'participant_id' => $participant->id,
                'old_conference_id' => $oldConferenceId,
                'new_conference_id' => $newConferenceId
            ]);

            // 1. Remove all session assignments
            $sessionCount = $participant->sessions()->count();
            if ($sessionCount > 0) {
                $participant->sessions()->detach();
                \Log::info("Removed {$sessionCount} session assignments for participant {$participant->id}");
            }

            // 2. Remove travel details
            if ($participant->travelDetails) {
                $participant->travelDetails()->delete();
                \Log::info("Removed travel details for participant {$participant->id}");
            }

            // 3. Remove room allocations
            $roomAllocationCount = $participant->roomAllocations()->count();
            if ($roomAllocationCount > 0) {
                $participant->roomAllocations()->delete();
                \Log::info("Removed {$roomAllocationCount} room allocations for participant {$participant->id}");
            }

            // 4. Remove checkins
            $checkinCount = $participant->checkins()->count();
            if ($checkinCount > 0) {
                $participant->checkins()->delete();
                \Log::info("Removed {$checkinCount} checkins for participant {$participant->id}");
            }

            // 5. Reset conference-specific participant fields
            $participant->update([
                'travel_form_submitted' => false,
                'travel_intent' => false,
                'registration_status' => 'pending',
                'approved' => false,
            ]);

            \Log::info("Reset conference-specific fields for participant {$participant->id}");

            // 6. Send notification about conference change
            $this->notifyConferenceChange($participant, $oldConferenceId, $newConferenceId);

        } catch (\Exception $e) {
            \Log::error('Failed to handle conference change for participant', [
                'participant_id' => $participant->id,
                'old_conference_id' => $oldConferenceId,
                'new_conference_id' => $newConferenceId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification about conference change
     */
    private function notifyConferenceChange(Participant $participant, $oldConferenceId, $newConferenceId)
    {
        try {
            $oldConference = \App\Models\Conference::find($oldConferenceId);
            $newConference = \App\Models\Conference::find($newConferenceId);

            $subject = "Conference Assignment Changed - {$newConference->name}";
            
            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Conference Assignment Update</h2>
                    
                    <p>Dear {$participant->user->first_name} {$participant->user->last_name},</p>
                    
                    <p>Your conference assignment has been changed by our administrative team.</p>
                    
                    <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #374151; margin-top: 0;'>Assignment Details:</h3>
                        <p><strong>Previous Conference:</strong> " . ($oldConference ? $oldConference->name : 'N/A') . "</p>
                        <p><strong>New Conference:</strong> {$newConference->name}</p>
                    </div>
                    
                    <div style='background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #92400e; margin-top: 0;'>Important Notice:</h3>
                        <p>Due to this conference change, the following have been reset:</p>
                        <ul style='color: #92400e;'>
                            <li>Session assignments</li>
                            <li>Travel details and arrangements</li>
                            <li>Room allocations</li>
                            <li>Check-in records</li>
                            <li>Registration status (reset to pending)</li>
                        </ul>
                        <p>Please review your new conference details and update your information as needed.</p>
                    </div>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('participants.show', $participant) . "' 
                           style='background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Your Profile
                        </a>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        If you have any questions about this change, please contact our support team.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
            ";
            
            // Use EmailTrackingService to send the email
            $emailTrackingService = app(\App\Services\EmailTrackingService::class);
            
            $emailTrackingService->sendTrackedEmailViaGmail(
                $participant->user->email,
                $subject,
                $emailBody,
                'conference_change',
                auth()->user(),
                $newConference,
                'Participant',
                $participant->id,
                'conference_change_notification'
            );
            
            \Log::info('Conference change notification sent', [
                'participant_id' => $participant->id,
                'old_conference_id' => $oldConferenceId,
                'new_conference_id' => $newConferenceId
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send conference change notification', [
                'participant_id' => $participant->id,
                'old_conference_id' => $oldConferenceId,
                'new_conference_id' => $newConferenceId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a new comment for a participant
     */
    public function storeComment(Request $request, Participant $participant)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Create the comment
        $comment = \App\Models\Comment::create([
            'user_id' => auth()->id(),
            'participant_id' => $participant->id,
            'conference_id' => $participant->conference_id,
            'content' => $request->comment,
        ]);

        // Create notification for the participant (if not the same user)
        if (auth()->id() !== $participant->user_id) {
            $participant->user->notifications()->create([
                'conference_id' => $participant->conference_id,
                'type' => 'comment_added',
                'title' => 'New Comment Added',
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' added a comment to your profile.',
                'data' => json_encode([
                    'comment_id' => $comment->id,
                    'participant_id' => $participant->id,
                    'commenter_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]),
            ]);

            // Send email notification
            $this->sendCommentEmailNotification($participant, $comment, auth()->user());
        }

        // Create notification for admins (if participant is commenting)
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin')) {
            $admins = \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'superadmin']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notifications()->create([
                    'conference_id' => $participant->conference_id,
                    'type' => 'participant_comment',
                    'title' => 'Participant Comment Added',
                    'message' => $participant->user->first_name . ' ' . $participant->user->last_name . ' added a comment to their profile.',
                    'data' => json_encode([
                        'comment_id' => $comment->id,
                        'participant_id' => $participant->id,
                        'participant_name' => $participant->user->first_name . ' ' . $participant->user->last_name,
                    ]),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Send email notification for new comment
     */
    private function sendCommentEmailNotification(Participant $participant, $comment, $commenter)
    {
        try {
            $subject = "New Comment Added to Your Profile - {$participant->conference->name}";
            
            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>New Comment Added</h2>
                    
                    <p>Dear {$participant->user->first_name} {$participant->user->last_name},</p>
                    
                    <p>A new comment has been added to your profile by {$commenter->first_name} {$commenter->last_name}.</p>
                    
                    <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #374151; margin-top: 0;'>Comment Details:</h3>
                        <p><strong>Commenter:</strong> {$commenter->first_name} {$commenter->last_name}</p>
                        <p><strong>Comment:</strong> {$comment->content}</p>
                        <p><strong>Date:</strong> " . $comment->created_at->format('M d, Y H:i') . "</p>
                    </div>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('participants.show', $participant) . "' 
                           style='background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Your Profile
                        </a>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        You can view and respond to comments on your profile page.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
            ";
            
            // Use EmailTrackingService to send the email
            $emailTrackingService = app(\App\Services\EmailTrackingService::class);
            
            $emailTrackingService->sendTrackedEmailViaGmail(
                $participant->user->email,
                $subject,
                $emailBody,
                'comment_added',
                $commenter,
                $participant->conference,
                'Participant',
                $participant->id,
                'comment_notification'
            );
            
        } catch (\Exception $e) {
            \Log::error('Failed to send comment email notification', [
                'participant_id' => $participant->id,
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 