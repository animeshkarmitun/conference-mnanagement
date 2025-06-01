<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['conference', 'participants'])->latest()->paginate(10);
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        $conferences = Conference::all();
        $participants = Participant::with('user')->get();
        return view('sessions.create', compact('conferences', 'participants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'participants' => 'array',
            'participants.*' => 'exists:participants,id',
        ]);

        $session = Session::create($validated);

        if ($request->has('participants')) {
            $session->participants()->sync($request->participants);
        }

        return redirect()->route('sessions.index')
            ->with('success', 'Session created successfully.');
    }

    public function show(Session $session)
    {
        $session->load(['conference', 'participants.user']);
        return view('sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        $conferences = Conference::all();
        $participants = Participant::with('user')->get();
        return view('sessions.edit', compact('session', 'conferences', 'participants'));
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'participants' => 'array',
            'participants.*' => 'exists:participants,id',
        ]);

        $session->update($validated);

        if ($request->has('participants')) {
            $session->participants()->sync($request->participants);
        } else {
            $session->participants()->detach();
        }

        return redirect()->route('sessions.index')
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(Session $session)
    {
        $session->participants()->detach();
        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session deleted successfully.');
    }
} 