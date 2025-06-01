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
            'visa_status' => 'required',
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
            'user_id' => 'required|exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
            'participant_type_id' => 'required|exists:participant_types,id',
            'visa_status' => 'required',
            'travel_form_submitted' => 'boolean',
            'bio' => 'nullable|string',
            'approved' => 'boolean',
            'organization' => 'nullable|string',
            'dietary_needs' => 'nullable|string',
            'travel_intent' => 'boolean',
            'registration_status' => 'required',
        ]);
        $participant->update($validated);
        return redirect()->route('participants.index')->with('success', 'Participant updated successfully.');
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
} 