<?php

namespace App\Http\Controllers;

use App\Models\ParticipantType;
use Illuminate\Http\Request;

class ParticipantTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participantTypes = ParticipantType::ordered()->get();
        $categories = ParticipantType::getCategories();
        
        return view('participant-types.index', compact('participantTypes', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ParticipantType::getCategories();
        return view('participant-types.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:participant_types,name',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:' . implode(',', array_keys(ParticipantType::getCategories())),
            'requires_approval' => 'boolean',
            'has_special_privileges' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['has_special_privileges'] = $request->has('has_special_privileges');

        ParticipantType::create($validated);

        return redirect()->route('participant-types.index')
            ->with('success', 'Participant type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParticipantType $participantType)
    {
        return view('participant-types.show', compact('participantType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParticipantType $participantType)
    {
        $categories = ParticipantType::getCategories();
        return view('participant-types.edit', compact('participantType', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParticipantType $participantType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:participant_types,name,' . $participantType->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:' . implode(',', array_keys(ParticipantType::getCategories())),
            'requires_approval' => 'boolean',
            'has_special_privileges' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['has_special_privileges'] = $request->has('has_special_privileges');

        $participantType->update($validated);

        return redirect()->route('participant-types.index')
            ->with('success', 'Participant type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParticipantType $participantType)
    {
        // Check if this participant type is being used
        if ($participantType->participants()->count() > 0) {
            return redirect()->route('participant-types.index')
                ->with('error', 'Cannot delete participant type that is being used by participants.');
        }

        $participantType->delete();

        return redirect()->route('participant-types.index')
            ->with('success', 'Participant type deleted successfully.');
    }
}
