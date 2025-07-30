<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\ParticipantType;
use App\Models\User;
use App\Models\TravelDetail;
use App\Models\Hotel;
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
        if ($request->has('type')) {
            $query->whereHas('participantType', function($q) use ($request) {
                $q->where('name', $request->type);
            });
        }
        $participants = $query->latest()->paginate(20);
        return view('participants.index', compact('participants'));
    }

    // Show create form
    public function create()
    {
        $conferences = Conference::all();
        $participantTypes = ParticipantType::all();
        $users = User::all();
        return view('participants.create', compact('conferences', 'participantTypes', 'users'));
    }

    // Store new participant
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
            'participant_type_id' => 'required|exists:participant_types,id',
            'visa_status' => 'required|in:required,not_required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
            'travel_form_submitted' => 'boolean',
            'bio' => 'nullable|string',
            'approved' => 'boolean',
            'organization' => 'nullable|string',
            'dietary_needs' => 'nullable|string',
            'travel_intent' => 'required',
            'registration_status' => 'required',
            'profile_picture' => 'nullable|image|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // If visa status is not 'issue', clear the description
        if ($validated['visa_status'] !== 'issue') {
            $validated['visa_issue_description'] = null;
        }

        // Handle file uploads and update user
        $user = User::findOrFail($request->user_id);
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $profilePicturePath;
        }
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $user->resume = $resumePath;
        }
        if ($request->filled('dietary_needs')) {
            $user->dietary_needs = $request->dietary_needs;
        }
        $user->save();

        $validated['travel_intent'] = $request->travel_intent == '1' ? true : false;

        Participant::create($validated);
        return redirect()->route('participants.index')->with('success', 'Participant created successfully.');
    }

    // Show participant details
    public function show(Participant $participant)
    {
        $participant->load(['user', 'conference', 'participantType', 'sessions']);
        $sessions = $participant->sessions;
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
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'conference_id' => 'sometimes|required|exists:conferences,id',
            'participant_type_id' => 'sometimes|required|exists:participant_types,id',
            'visa_status' => 'required|in:required,not_required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
            'travel_form_submitted' => 'sometimes|boolean',
            'bio' => 'nullable|string',
            'approved' => 'sometimes|boolean',
            'organization' => 'nullable|string',
            'dietary_needs' => 'nullable|string',
            'travel_intent' => 'sometimes|boolean',
            'registration_status' => 'sometimes|required',
        ]);

        // If visa status is not 'issue', clear the description
        if ($validated['visa_status'] !== 'issue') {
            $validated['visa_issue_description'] = null;
        }

        $participant->update($validated);
        
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
        return view('participants.profile', compact('participant'));
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
} 