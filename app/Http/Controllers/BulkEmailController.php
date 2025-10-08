<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Services\EmailTrackingService;
use App\Services\EmailTemplateService;
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
            $emailTemplateService = app(EmailTemplateService::class);
            $sentCount = 0;
            $failedCount = 0;
            $errors = [];
            
            foreach ($participants as $participant) {
                try {
                    // Prepare variables for template
                    $variables = [
                        'first_name' => $participant->user->first_name ?? 'User',
                        'last_name' => $participant->user->last_name ?? '',
                        'conference_name' => $conference->name ?? 'Conference',
                        'custom_message' => $request->message,
                    ];

                    // Get template from service
                    $template = $emailTemplateService->processTemplate(
                        \App\Models\Email::TYPE_CONFERENCE_UPDATE,
                        $variables
                    );

                    // Build full email body with custom message
                    $fullBody = $this->buildBulkEmailBody($template, $request->message);

                    $emailTrackingService->sendTrackedEmailViaGmail(
                        $participant->user->email,
                        $template['subject'],
                        $fullBody,
                        \App\Models\Email::TYPE_CONFERENCE_UPDATE,
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

    /**
     * Build full email body for bulk emails
     */
    private function buildBulkEmailBody(array $template, string $customMessage): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
            <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <p>{$template['greeting']}</p>
                
                <div style='margin: 20px 0;'>
                    <div style='background-color: #f0f9ff; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6;'>
                        <h3 style='color: #1e40af; margin: 0 0 10px 0; font-size: 18px;'>Conference Update</h3>
                        <div style='color: #374151; line-height: 1.6; white-space: pre-line;'>{$customMessage}</div>
                    </div>
                </div>
                
                <p style='margin: 20px 0;'>{$template['closing']}</p>
                <p style='margin: 20px 0;'>{$template['signature']}</p>
                
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                    This is an automated notification from the Conference Management System.
                </p>
            </div>
        </div>
        ";
    }
} 