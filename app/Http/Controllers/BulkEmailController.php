<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BulkEmailController extends Controller
{
    public function show(Request $request)
    {
        $participantIds = $request->get('participant_ids', []);
        $selectedConferenceId = $request->get('conference_id');
        $participants = collect([]); // Initialize as empty collection
        $conferences = Conference::orderBy('name')->get(); // Load all conferences
        
        if (!empty($participantIds)) {
            $participants = Participant::with(['user', 'conference', 'participantType'])
                ->whereIn('id', $participantIds)
                ->get();
        } elseif ($selectedConferenceId) {
            $participants = Participant::with(['user', 'conference', 'participantType'])
                ->where('conference_id', $selectedConferenceId)
                ->get();
        }
        
        return view('bulk-email', compact('participants', 'conferences', 'selectedConferenceId'));
    }
    
    public function getParticipants(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['participants' => []]);
        }
        
        $participants = Participant::with(['user', 'conference', 'participantType'])
            ->where('conference_id', $conferenceId)
            ->get()
            ->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                    'email' => $participant->user->email,
                    'conference_name' => $participant->conference->name ?? 'No Conference',
                    'participant_type' => $participant->participantType->name ?? 'Unknown',
                    'registration_status' => $participant->registration_status ?? 'pending'
                ];
            });
        
        return response()->json(['participants' => $participants]);
    }
    
    public function send(Request $request)
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'conference_id' => 'nullable|exists:conferences,id'
        ]);
        
        try {
            $participants = Participant::with(['user', 'conference'])
                ->whereIn('id', $request->participant_ids)
                ->get();
            
            $conference = $request->conference_id ? Conference::find($request->conference_id) : null;
            $emailTrackingService = app(EmailTrackingService::class);
            $sentCount = 0;
            $failedCount = 0;
            $errors = [];
            
            foreach ($participants as $participant) {
                try {
                    $emailTrackingService->sendTrackedEmailViaGmail(
                        $participant->user->email,
                        $request->subject,
                        $request->message,
                        \App\Models\Email::TYPE_GENERAL,
                        Auth::user(),
                        $conference,
                        'Participant',
                        $participant->id,
                        'bulk_email'
                    );
                    $sentCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Failed to send to {$participant->user->email}: " . $e->getMessage();
                }
            }
            
            $message = "Bulk email sent successfully! Sent: {$sentCount}";
            if ($failedCount > 0) {
                $message .= ", Failed: {$failedCount}";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Bulk email failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send bulk emails: ' . $e->getMessage());
        }
    }
} 