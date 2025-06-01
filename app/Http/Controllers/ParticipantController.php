<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\ParticipantType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'travel_intent' => 'boolean',
            'registration_status' => 'required',
        ]);
        Participant::create($validated);
        return redirect()->route('participants.index')->with('success', 'Participant created successfully.');
    }

    // Show participant details
    public function show(Participant $participant)
    {
        $participant->load(['user', 'conference', 'participantType', 'sessions']);
        return view('participants.show', compact('participant'));
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
} 