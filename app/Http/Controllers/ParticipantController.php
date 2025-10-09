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
                        $participant->id,
                        $user->first_name ?? '',
                        $user->last_name ?? '',
                        $user->email ?? '',
                        $user->gender ?? '',
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
        // Get participant type for conditional validation
        $type = null;
        if ($request->participant_type_id) {
            $type = \App\Models\ParticipantType::find($request->participant_type_id);
        }

        // Build validation rules dynamically
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            // All other fields are optional
            'gender' => 'nullable|in:male,female,prefer_not_to_say',
            'date_of_birth' => 'nullable|date',
            'organization' => 'nullable|string|max:255',
            'dietary_needs' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            // Enhanced participant fields - all optional
            'pronoun' => 'nullable|in:he_him,she_her,they_them',
            'contact_no' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'field_of_work_study' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'organization_institution' => 'nullable|string|max:255',
            'is_student' => 'nullable|boolean',
            'year' => 'nullable|in:honors_final_year,masters',
            'department_name' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'home_district' => 'nullable|string|max:100',
            'nid_passport_birth_certificate' => 'nullable|image|max:300',
            'how_found_bobc' => 'nullable|in:social_media,bobc_cgs_website,friend_teacher_department,traditional_media,other',
            'attended_previous_bobc' => 'nullable|boolean',
            'expertise_interests' => 'nullable|string|max:1000',
        ];

        // Add conditional validation rules based on participant type
        if ($type) {
            if ($type->category === 'press') {
                $validationRules['media_type'] = 'required|in:print,television,online_portal';
            } elseif ($type->category === 'presenter') {
                $validationRules['other_contact_type'] = 'nullable|in:whatsapp,telegram,signal';
                $validationRules['other_contact_no'] = 'nullable|string|max:50';
                $validationRules['dietary_requirements'] = 'nullable|in:veg,non_veg,vegan,others';
                $validationRules['dietary_requirements_other'] = 'nullable|required_if:dietary_requirements,others|string|max:255';
                $validationRules['sector'] = 'nullable|in:academia,government,international_organization,media,ngo,private,think_tank';
                $validationRules['areas_of_expertise'] = 'nullable|string';
                $validationRules['preferred_topic'] = 'nullable|string';
                $validationRules['has_valid_passport'] = 'nullable|in:0,1';
                $validationRules['had_visa_issue_bd'] = 'nullable|in:0,1';
                $validationRules['visa_issue_explanation'] = 'nullable|required_if:had_visa_issue_bd,1|string';
            }
        }

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser) {
            // Check if user already has a participant in this conference
            $existingParticipant = $existingUser->participants()
                ->where('conference_id', $request->conference_id)
                ->where('status', 'active')
                ->first();
                
            if ($existingParticipant) {
                return back()
                    ->withErrors(['conference_id' => 'You already have an active participant profile for this conference. Please use the participant profiles management to create a different profile type.'])
                    ->withInput($request->all());
            }
            
            // User exists - validate only participant data
            $participantValidated = $request->validate([
                'conference_id' => 'required|exists:conferences,id',
                'participant_type_id' => 'required|exists:participant_types,id',
                'profile_name' => 'nullable|string|max:255',
                'profile_type' => 'nullable|in:personal,professional,academic,media,speaker',
                'profile_description' => 'nullable|string|max:1000',
                'visa_status' => 'nullable|in:required,not_required,pending,approved,issue',
                'visa_issue_description' => 'nullable|string|max:1000',
                'bio' => 'nullable|string',
                'approved' => 'nullable|boolean',
                'travel_intent' => 'nullable|in:national,international',
                'registration_status' => 'nullable|in:pending,approved,rejected',
                'category' => 'nullable|string|max:50',
                'dietary_needs_other' => 'nullable|string|max:255',
                'hashtags' => 'nullable|string|max:1000',
            ]);

            // Validate travel dates separately
            $travelDatesValidated = $request->validate([
                'arrival_date' => 'nullable|date',
                'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            ]);

            $user = $existingUser;
            $isAutoGenerated = false;
        } else {
            // User doesn't exist - validate all user data with unique email constraint
            $validationRules['email'] = 'required|string|email|max:255|unique:users';
            $userValidated = $request->validate($validationRules);

            // Validate participant data - Only essential fields required
            $participantValidated = $request->validate([
                'conference_id' => 'required|exists:conferences,id',
                'participant_type_id' => 'required|exists:participant_types,id',
                'profile_name' => 'nullable|string|max:255',
                'profile_type' => 'nullable|in:personal,professional,academic,media,speaker',
                'profile_description' => 'nullable|string|max:1000',
                'visa_status' => 'nullable|in:required,not_required,pending,approved,issue',
                'visa_issue_description' => 'nullable|string|max:1000',
                'bio' => 'nullable|string',
                'approved' => 'nullable|boolean',
                'travel_intent' => 'nullable|in:national,international',
                'registration_status' => 'nullable|in:pending,approved,rejected',
                'category' => 'nullable|string|max:50',
                'dietary_needs_other' => 'nullable|string|max:255',
                'hashtags' => 'nullable|string|max:1000',
            ]);

            // Validate travel dates separately
            $travelDatesValidated = $request->validate([
                'arrival_date' => 'nullable|date',
                'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            ]);

            // Generate password if not provided
            $password = $userValidated['password'];
            $isAutoGenerated = false;
            if (empty($password)) {
                $password = \Str::random(12); // Generate a 12-character random password
                $isAutoGenerated = true;
            }

            // Create the user first
            $user = User::create([
                'first_name' => $userValidated['first_name'],
                'last_name' => $userValidated['last_name'],
                'email' => $userValidated['email'],
                'password' => bcrypt($password),
                'gender' => $userValidated['gender'] ?? null,
                'date_of_birth' => $userValidated['date_of_birth'] ?? null,
                'organization' => $userValidated['organization'] ?? null,
                'dietary_needs' => $userValidated['dietary_needs'] ?? null,
                // New enhanced participant fields
                'pronoun' => $userValidated['pronoun'] ?? null,
                'contact_no' => $userValidated['contact_no'] ?? null,
                'whatsapp_no' => $userValidated['whatsapp_no'] ?? null,
                'field_of_work_study' => $userValidated['field_of_work_study'] ?? null,
                'designation' => $userValidated['designation'] ?? null,
                'organization_institution' => $userValidated['organization_institution'] ?? null,
                'is_student' => $userValidated['is_student'] ?? null,
                'year' => $userValidated['year'] ?? null,
                'department_name' => $userValidated['department_name'] ?? null,
                'institution_name' => $userValidated['institution_name'] ?? null,
                'address' => $userValidated['address'] ?? null,
                'home_district' => $userValidated['home_district'],
                'how_found_bobc' => $userValidated['how_found_bobc'],
                'attended_previous_bobc' => $userValidated['attended_previous_bobc'],
                'expertise_interests' => $userValidated['expertise_interests'],
                // Media/Speaker conditional fields
                'media_type' => $request->media_type,
                'other_contact_type' => $request->other_contact_type,
                'other_contact_no' => $request->other_contact_no,
                'dietary_requirements' => $request->dietary_requirements,
                'dietary_requirements_other' => $request->dietary_requirements_other,
                'sector' => $request->sector,
                'areas_of_expertise' => $request->areas_of_expertise,
                'preferred_topic' => $request->preferred_topic,
                'linkedin_link' => $request->linkedin_link,
                'twitter_link' => $request->twitter_link,
                'facebook_link' => $request->facebook_link,
                'has_valid_passport' => $request->has_valid_passport,
                'had_visa_issue_bd' => $request->had_visa_issue_bd,
                'visa_issue_explanation' => $request->visa_issue_explanation,
            ]);

            // Assign attendee role to the user if they don't have any roles
            if (!$user->hasAnyRole()) {
                $attendeeRole = \App\Models\Role::where('name', 'attendee')->first();
                if ($attendeeRole) {
                    $user->roles()->attach($attendeeRole->id);
                }
            }

            // Handle file uploads for the user
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $profilePicturePath;
            }
            if ($request->hasFile('nid_passport_birth_certificate')) {
                $nidPath = $request->file('nid_passport_birth_certificate')->store('nid_documents', 'public');
                $user->nid_passport_birth_certificate = $nidPath;
            }
            $user->save();
        }

        // If visa status is not 'issue', clear the description
        if ($participantValidated['visa_status'] !== 'issue') {
            $participantValidated['visa_issue_description'] = null;
        }

        // Add user_id to participant data
        $participantValidated['user_id'] = $user->id;
        $participantValidated['travel_intent'] = $request->travel_intent ?? 'national';
        $participantValidated['hashtags'] = $request->hashtags_input ?? null;
        
        // Set multi-profile defaults
        $participantValidated['status'] = 'active';
        $participantValidated['is_primary'] = false; // New participants are not primary by default
        
        // Generate profile name if not provided
        if (empty($participantValidated['profile_name'])) {
            $conference = \App\Models\Conference::find($participantValidated['conference_id']);
            $participantValidated['profile_name'] = $user->first_name . ' ' . $user->last_name . ' - ' . $conference->name;
        }
        
        // Set profile type if not provided
        if (empty($participantValidated['profile_type'])) {
            $participantValidated['profile_type'] = 'personal';
        }

        // Create the participant
        $participant = Participant::create($participantValidated);
        
        // If this is the first participant for the user, make it primary
        if ($user->participants()->count() === 1) {
            $participant->update(['is_primary' => true]);
        }
        
        // Create travel details if travel dates are provided
        if ($travelDatesValidated['arrival_date'] || $travelDatesValidated['departure_date']) {
            \App\Models\TravelDetail::create([
                'participant_id' => $participant->id,
                'arrival_date' => $travelDatesValidated['arrival_date'],
                'departure_date' => $travelDatesValidated['departure_date'],
            ]);
        }
        
        // Send welcome email to the participant
        try {
            $emailTrackingService = app(\App\Services\EmailTrackingService::class);
            $emailTemplateService = app(\App\Services\EmailTemplateService::class);
            $conference = \App\Models\Conference::find($participantValidated['conference_id']);
            $participantType = \App\Models\ParticipantType::find($participantValidated['participant_type_id']);
            
            // Prepare variables for template
            $variables = [
                'first_name' => $user->first_name ?? 'User',
                'last_name' => $user->last_name ?? '',
                'conference_name' => $conference->name ?? 'the Conference',
                'participant_type' => $participantType->name ?? 'N/A',
                'serial_number' => $serialNumber,
                'email' => $user->email,
                'password' => $isAutoGenerated ? $password : null,
                'profile_url' => route('participants.show', $participant),
            ];

            // Get template from service
            $template = $emailTemplateService->processTemplate(
                \App\Models\Email::TYPE_PROFILE_UPDATE,
                $variables
            );

            // Build custom registration email body with template structure
            $passwordSection = '';
            if ($isAutoGenerated && $password) {
                $passwordSection = "
                        <div style='background-color: #fef3c7; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                            <h3 style='color: #92400e; margin: 0 0 10px 0; font-size: 18px;'>Login Credentials</h3>
                            <p style='margin: 5px 0; color: #92400e;'><strong>Email:</strong> {$user->email}</p>
                            <p style='margin: 5px 0; color: #92400e;'><strong>Password:</strong> {$password}</p>
                            <p style='margin: 10px 0 0 0; color: #92400e; font-size: 14px;'><em>Please change this password after your first login for security.</em></p>
                        </div>";
            }

            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
                    <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <div style='text-align: center; margin-bottom: 30px;'>
                            <h1 style='color: #1f2937; margin: 0; font-size: 28px;'>Welcome to {$conference->name}!</h1>
                            <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 16px;'>Your registration has been successfully confirmed</p>
                        </div>
                        
                        <div style='background-color: #f0f9ff; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6;'>
                            <h3 style='color: #1e40af; margin: 0 0 10px 0; font-size: 18px;'>Registration Details</h3>
                            <p style='margin: 5px 0; color: #374151;'><strong>Name:</strong> {$user->first_name} {$user->last_name}</p>
                            <p style='margin: 5px 0; color: #374151;'><strong>Email:</strong> {$user->email}</p>
                            <p style='margin: 5px 0; color: #374151;'><strong>Participant Type:</strong> {$participantType->name}</p>
                            <p style='margin: 5px 0; color: #374151;'><strong>Serial Number:</strong> {$serialNumber}</p>
                        </div>
                        
                        {$passwordSection}
                        
                        <div style='margin: 30px 0;'>
                            <h3 style='color: #1f2937; margin: 0 0 15px 0; font-size: 18px;'>Next Steps</h3>
                            <ul style='color: #374151; line-height: 1.6; padding-left: 20px;'>
                                <li>Keep this email for your records</li>
                                <li>Check your email regularly for conference updates</li>
                                <li>Complete your profile with additional information if needed</li>
                                <li>Contact us if you have any questions</li>
                            </ul>
                        </div>
                        
                        <div style='margin: 30px 0; text-align: center;'>
                            <a href='" . route('participants.show', $participant) . "' 
                               style='background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                                View Your Profile
                            </a>
                        </div>
                        
                        <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                            If you have any questions about your registration, please contact our support team.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                        <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                            This is an automated notification from the Conference Management System.
                        </p>
                    </div>
                </div>
            ";
            
            $emailTrackingService->sendTrackedEmailViaGmail(
                $user->email,
                $template['subject'],
                $emailBody,
                \App\Models\Email::TYPE_PROFILE_UPDATE,
                auth()->user(),
                $conference,
                'Participant',
                $participant->id,
                'participant_welcome_notification'
            );
            
            \Log::info('Participant welcome email sent', [
                'participant_id' => $participant->id,
                'user_email' => $user->email,
                'conference_id' => $conference->id ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send participant welcome email', [
                'participant_id' => $participant->id,
                'user_email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
        
        $successMessage = 'Participant created successfully and welcome email sent.';
        if ($isAutoGenerated) {
            $successMessage .= ' Password was auto-generated and sent via email.';
        }
        
        return redirect()->route('participants.index')->with('success', $successMessage);
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
        $hotels = Hotel::with('rooms.roomType')->get();
        $roomTypes = \App\Models\RoomType::where('is_active', true)->get();
        
        // Determine if the current user is an admin/superadmin viewing someone else's profile
        $isAdminViewing = (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) && 
                         auth()->id() !== $participant->user_id;
        
        if ($isAdminViewing) {
            // Use admin layout for admin viewing participant details
            return view('participants.show-admin', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels', 'roomTypes', 'availableSessions'));
        } else {
            // Use participant layout for participants viewing their own profile
            return view('participants.show', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels', 'roomTypes', 'availableSessions'));
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
                'travel_intent' => 'nullable|in:national,international',
            ]);

            // Validate travel dates separately
            $travelDatesValidated = $request->validate([
                'arrival_date' => 'nullable|date',
                'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            ]);

            // Validate user data for personal update
            $userValidated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:255|unique:users,email,' . $participant->user_id,
            // Enhanced participant fields
            'gender' => 'nullable|in:male,female,prefer_not_to_say',
            'contact_no' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'field_of_work_study' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'organization_institution' => 'nullable|string|max:255',
            'is_student' => 'nullable|boolean',
            'year' => 'nullable|in:honors_final_year,masters',
            'department_name' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'home_district' => 'nullable|string|max:100',
            'how_found_bobc' => 'nullable|in:social_media,bobc_cgs_website,friend_teacher_department,traditional_media,other',
            'attended_previous_bobc' => 'nullable|boolean',
            'expertise_interests' => 'nullable|string|max:1000',
                // Media fields (if participant type is press)
                'media_type' => 'nullable|in:print,television,online_portal',
                // Speaker fields (if participant type is presenter)
                'other_contact_type' => 'nullable|in:whatsapp,telegram,signal',
                'other_contact_no' => 'nullable|string|max:50',
                'sector' => 'nullable|in:academia,government,international_organization,media,ngo,private,think_tank',
                'areas_of_expertise' => 'nullable|string',
                'preferred_topic' => 'nullable|string',
                'linkedin_link' => 'nullable|url',
                'twitter_link' => 'nullable|url',
                'facebook_link' => 'nullable|url',
                'has_valid_passport' => 'nullable|in:0,1',
                'had_visa_issue_bd' => 'nullable|in:0,1',
                'visa_issue_explanation' => 'nullable|string',
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
                'approved' => 'boolean',
                'travel_intent' => 'nullable|in:national,international',
                'registration_status' => 'required|in:pending,approved,rejected',
            ]);

            // Validate travel dates separately
            $travelDatesValidated = $request->validate([
                'arrival_date' => 'nullable|date',
                'departure_date' => 'nullable|date|after_or_equal:arrival_date',
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
                // Enhanced participant fields
                'gender' => $userValidated['gender'] ?? null,
                'contact_no' => $userValidated['contact_no'] ?? null,
                'whatsapp_no' => $userValidated['whatsapp_no'] ?? null,
                'date_of_birth' => $userValidated['date_of_birth'] ?? null,
                'address' => $userValidated['address'] ?? null,
                'field_of_work_study' => $userValidated['field_of_work_study'] ?? null,
                'designation' => $userValidated['designation'] ?? null,
                'organization_institution' => $userValidated['organization_institution'] ?? null,
                'is_student' => $userValidated['is_student'] ?? null,
                'year' => $userValidated['year'] ?? null,
                'department_name' => $userValidated['department_name'] ?? null,
                'institution_name' => $userValidated['institution_name'] ?? null,
                'home_district' => $userValidated['home_district'] ?? null,
                'how_found_bobc' => $userValidated['how_found_bobc'] ?? null,
                'attended_previous_bobc' => $userValidated['attended_previous_bobc'] ?? null,
                'expertise_interests' => $userValidated['expertise_interests'] ?? null,
                // Media fields
                'media_type' => $userValidated['media_type'] ?? null,
                // Speaker fields
                'other_contact_type' => $userValidated['other_contact_type'] ?? null,
                'other_contact_no' => $userValidated['other_contact_no'] ?? null,
                'sector' => $userValidated['sector'] ?? null,
                'areas_of_expertise' => $userValidated['areas_of_expertise'] ?? null,
                'preferred_topic' => $userValidated['preferred_topic'] ?? null,
                'linkedin_link' => $userValidated['linkedin_link'] ?? null,
                'twitter_link' => $userValidated['twitter_link'] ?? null,
                'facebook_link' => $userValidated['facebook_link'] ?? null,
                'has_valid_passport' => $userValidated['has_valid_passport'] ?? null,
                'had_visa_issue_bd' => $userValidated['had_visa_issue_bd'] ?? null,
                'visa_issue_explanation' => $userValidated['visa_issue_explanation'] ?? null,
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

        // Handle travel intent field
        $participantValidated['travel_intent'] = $request->travel_intent ?? 'national';
        
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
        
        // Update or create travel details
        if ($travelDatesValidated['arrival_date'] || $travelDatesValidated['departure_date']) {
            $travelDetail = $participant->travelDetails;
            if ($travelDetail) {
                $travelDetail->update([
                    'arrival_date' => $travelDatesValidated['arrival_date'],
                    'departure_date' => $travelDatesValidated['departure_date'],
                ]);
            } else {
                \App\Models\TravelDetail::create([
                    'participant_id' => $participant->id,
                    'arrival_date' => $travelDatesValidated['arrival_date'],
                    'departure_date' => $travelDatesValidated['departure_date'],
                ]);
            }
        } else {
            // If no travel dates provided and travel intent is national, remove travel details
            if ($participant->travel_intent === 'national') {
                $participant->travelDetails?->delete();
            }
        }
        
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
        $user = Auth::user();
        
        // Get active participant profile (either from session or primary)
        $participant = $user->getActiveParticipantProfile();
        
        if (!$participant) {
            // If no active participant, redirect to profile management
            return redirect()->route('participant-profiles.index')
                ->with('error', 'No active participant profile found. Please create or select a participant profile.');
        }
        
        $sessions = $participant->sessions()->withPivot('role')->get();
        \Log::info('Sessions loaded for participant ' . $participant->id . ': ' . $sessions->count());
        $notifications = $participant->user->notifications()->latest()->get();
        \Log::info('Notifications loaded for participant ' . $participant->id . ': ' . $notifications->count());
        $comments = $participant->comments()->with('user')->latest()->get();
        \Log::info('Comments loaded for participant ' . $participant->id . ': ' . $comments->count());
        $travelDetail = $participant->travelDetails;
        $hotels = Hotel::with('rooms.roomType')->get();
        $roomTypes = \App\Models\RoomType::where('is_active', true)->get();
        
        // Get all user's participant profiles for profile switcher
        $allParticipants = $user->participants()
            ->with(['conference', 'participantType'])
            ->where('status', 'active')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('participants.show', compact('participant', 'sessions', 'notifications', 'comments', 'travelDetail', 'hotels', 'roomTypes', 'allParticipants'));
    }

    /**
     * Switch to a different participant profile
     */
    public function switchProfile(Request $request, $participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant || $participant->status !== 'active') {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        if ($user->setActiveParticipantProfile($participantId)) {
            return back()->with('success', 'Switched to ' . $participant->getProfileDisplayName());
        }

        return back()->withErrors(['error' => 'Failed to switch participant profile.']);
    }

    public function updateTravel(Request $request, Participant $participant)
    {
        // Restrict to admins only
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'flight_info' => 'nullable|string',
            'hotel_id' => 'nullable|exists:hotels,id',
            'room_id' => 'nullable|exists:rooms,id',
            'extra_nights' => 'nullable|integer|min:0',
            'room_check_in' => 'nullable|date',
            'room_check_out' => 'nullable|date|after:room_check_in',
            'travel_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'departure_date.after_or_equal' => 'Departure date must be on or after arrival date.',
            'room_check_out.after' => 'Room check-out date must be after check-in date.'
        ]);

        $travelDetail = $participant->travelDetails ?: $participant->travelDetails()->make();

        $travelDetail->arrival_date = $validated['arrival_date'] ?? null;
        $travelDetail->departure_date = $validated['departure_date'] ?? null;
        $travelDetail->flight_info = $validated['flight_info'] ?? null;
        $travelDetail->hotel_id = $validated['hotel_id'] ?? null;
        $travelDetail->room_id = $validated['room_id'] ?? null;
        $travelDetail->extra_nights = $validated['extra_nights'] ?? 0;
        $travelDetail->room_check_in = $validated['room_check_in'] ?? null;
        $travelDetail->room_check_out = $validated['room_check_out'] ?? null;

        $hasDocuments = $request->hasFile('travel_documents');
        if ($hasDocuments) {
            $travelDocumentPath = $request->file('travel_documents')->store('travel_documents', 'public');
            $travelDetail->travel_documents = $travelDocumentPath;
        }

        $travelDetail->participant_id = $participant->id;
        $travelDetail->save();

        // Sync travel details to room allocation (if room check-in/out times are provided)
        if ($validated['room_check_in'] || $validated['room_check_out']) {
            $this->syncTravelDetailsToRoomAllocation($participant, $travelDetail);
        }

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
        ]);

        try {
            $participant->update([
                'registration_status' => $validated['registration_status'],
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
     * Update visa status for a participant
     */
    public function updateVisaStatus(Request $request, Participant $participant)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validated = $request->validate([
            'visa_status' => 'required|in:not_required,required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
        ]);

        try {
            $participant->update([
                'visa_status' => $validated['visa_status'],
                'visa_issue_description' => $validated['visa_issue_description'] ?? null,
            ]);

            $statusText = ucwords(str_replace('_', ' ', $validated['visa_status']));
            return response()->json([
                'success' => true, 
                'message' => "Visa status updated to {$statusText} successfully"
            ]);
        } catch (\Exception $e) {
            \Log::error('Visa status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update visa status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to a participant
     */
    public function sendNotification(Request $request, Participant $participant)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error',
        ]);

        try {
            $participant->user->notifications()->create([
                'message' => $validated['message'],
                'type' => $validated['type'],
                'read_status' => false,
                'data' => [
                    'sent_by' => $user->id,
                    'sent_by_name' => $user->first_name . ' ' . $user->last_name,
                ]
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Notification sent successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Notification send failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for a participant
     */
    public function markNotificationsRead(Request $request, Participant $participant)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            $participant->user->notifications()->update(['read_status' => true]);

            return response()->json([
                'success' => true, 
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            \Log::error('Mark notifications read failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific notification
     */
    public function deleteNotification(Request $request, Participant $participant, $notificationId)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            $notification = $participant->user->notifications()->findOrFail($notificationId);
            $notification->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete notification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroyComment(Request $request, Participant $participant, $commentId)
    {
        // Check permissions
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || in_array('super_admin', $userRoles) || in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            $comment = $participant->comments()->findOrFail($commentId);
            $comment->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete comment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete comment: ' . $e->getMessage()
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
                'travel_intent' => 'national',
                'registration_status' => 'pending',
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

    /**
     * Check if email is unique via AJAX
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_id' => 'nullable|exists:users,id' // For update operations
        ]);

        $email = $request->email;
        $userId = $request->user_id;

        // Check if email exists
        $query = User::where('email', $email);
        
        // If updating, exclude current user from check
        if ($userId) {
            $query->where('id', '!=', $userId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email is already taken' : 'Email is available'
        ]);
    }

    /**
     * Sync travel details to room allocation
     * Updates room allocation with data from travel details
     */
    private function syncTravelDetailsToRoomAllocation($participant, $travelDetail)
    {
        try {
            $roomAllocation = $participant->roomAllocations()->first();
            
            if (!$roomAllocation) {
                $roomAllocation = new \App\Models\RoomAllocation();
                $roomAllocation->participant_id = $participant->id;
            }
            
            // Update hotel information from travel details
            if ($travelDetail->hotel_id) {
                $roomAllocation->hotel_id = $travelDetail->hotel_id;
            }
            
            // Update check-in/check-out times from travel details
            if ($travelDetail->room_check_in) {
                $roomAllocation->check_in = $travelDetail->room_check_in;
            }
            if ($travelDetail->room_check_out) {
                $roomAllocation->check_out = $travelDetail->room_check_out;
            }
            
            $roomAllocation->save();
            
            \Log::info('Travel details synced to room allocation', [
                'participant_id' => $participant->id,
                'travel_detail_id' => $travelDetail->id,
                'room_allocation_id' => $roomAllocation->id,
                'check_in' => $travelDetail->room_check_in,
                'check_out' => $travelDetail->room_check_out
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to sync travel details to room allocation', [
                'participant_id' => $participant->id,
                'travel_detail_id' => $travelDetail->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 