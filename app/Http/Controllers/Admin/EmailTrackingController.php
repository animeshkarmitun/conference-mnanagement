<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Conference;
use App\Models\Role;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EmailTrackingController extends Controller
{
    protected EmailTrackingService $emailTrackingService;

    public function __construct(EmailTrackingService $emailTrackingService)
    {
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Display email tracking dashboard
     */
    public function index(Request $request): View
    {
        $conferenceId = $request->get('conference_id');
        $days = $request->get('days', 30);
        $recipientEmail = $request->get('recipient_email');
        $role = $request->get('role');
        $type = $request->get('type');
        $subject = $request->get('subject');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $conferences = Conference::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $selectedConference = $conferenceId ? Conference::find($conferenceId) : null;

        // Get email statistics
        $stats = $this->emailTrackingService->getEmailStats($conferenceId, $days);
        $statsByType = $this->emailTrackingService->getEmailStatsByType($conferenceId, $days);

        // Get recent emails
        $emails = $this->emailTrackingService->getRecentEmails(
            20,
            $conferenceId,
            null,
            $recipientEmail,
            $role,
            $type,
            $subject,
            $status,
            $sortBy,
            $sortDir
        );

        return view('admin.email-tracking.index', compact(
            'conferences',
            'roles',
            'selectedConference',
            'stats',
            'statsByType',
            'emails',
            'conferenceId',
            'days',
            'recipientEmail',
            'role',
            'type',
            'subject',
            'status',
            'sortBy',
            'sortDir'
        ));
    }

    /**
     * Get email statistics as JSON
     */
    public function stats(Request $request): JsonResponse
    {
        $conferenceId = $request->get('conference_id');
        $days = $request->get('days', 30);

        $stats = $this->emailTrackingService->getEmailStats($conferenceId, $days);
        $statsByType = $this->emailTrackingService->getEmailStatsByType($conferenceId, $days);

        return response()->json([
            'overall' => $stats,
            'by_type' => $statsByType,
        ]);
    }

    /**
     * Get emails list as JSON
     */
    public function emails(Request $request): JsonResponse
    {
        $conferenceId = $request->get('conference_id');
        $perPage = $request->get('per_page', 20);
        $recipientEmail = $request->get('recipient_email');
        $role = $request->get('role');
        $type = $request->get('type');
        
        $emails = $this->emailTrackingService->getRecentEmails($perPage, $conferenceId, null, $recipientEmail, $role, $type);

        return response()->json($emails);
    }

    /**
     * Get participant conversation
     */
    public function getParticipantConversation(Request $request): JsonResponse
    {
        $participantEmail = $request->input('participant_email');
        $conferenceId = $request->input('conference_id');

        if (!$participantEmail) {
            return response()->json(['error' => 'Participant email required'], 400);
        }

        $conversation = $this->emailTrackingService->getParticipantConversation($participantEmail, $conferenceId);
        $stats = $this->emailTrackingService->getParticipantConversationStats($participantEmail, $conferenceId);

        return response()->json([
            'conversation' => $conversation,
            'stats' => $stats
        ]);
    }

    /**
     * Get all participants with conversations
     */
    public function getParticipantsWithConversations(Request $request): JsonResponse
    {
        $conferenceId = $request->input('conference_id');
        $participants = $this->emailTrackingService->getAllParticipantsWithConversations($conferenceId);

        return response()->json($participants);
    }

    /**
     * Sync Gmail messages for participant
     */
    public function syncParticipantGmail(Request $request): JsonResponse
    {
        $participantEmail = $request->input('participant_email');
        $conferenceId = $request->input('conference_id');

        if (!$participantEmail) {
            return response()->json(['error' => 'Participant email required'], 400);
        }

        try {
            $gmailIncomingService = app(\App\Services\GmailIncomingEmailService::class);
            $adminUser = \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'superadmin']);
            })->whereNotNull('google_token')->first();

            if (!$adminUser) {
                return response()->json(['error' => 'No admin user with Gmail token found'], 400);
            }

            $googleService = app(\App\Services\GoogleService::class);
            $googleService->setAccessToken(json_decode($adminUser->google_token, true));

            $syncedCount = $gmailIncomingService->syncParticipantGmailMessages($participantEmail);

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'participant_email' => $participantEmail
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send email to participant via Gmail
     */
    public function sendEmailToParticipant(Request $request): JsonResponse
    {
        $request->validate([
            'participant_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'conference_id' => 'nullable|exists:conferences,id',
            'email_type' => 'nullable|string'
        ]);

        try {
            $email = $this->emailTrackingService->sendTrackedEmailViaGmail(
                $request->participant_email,
                $request->subject,
                $request->body,
                $request->email_type ?? \App\Models\Email::TYPE_GENERAL,
                auth()->user(),
                $request->conference_id ? \App\Models\Conference::find($request->conference_id) : null
            );

            return response()->json([
                'success' => true,
                'email_id' => $email->id,
                'message' => 'Email sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show email details
     */
    public function show(Email $email): View
    {
        $email->load(['user', 'conference']);
        
        // Safely load related model if it exists and is valid
        if ($email->related_model_type && $email->related_model_id) {
            try {
                $email->load('relatedModel');
            } catch (\Exception $e) {
                // If related model class doesn't exist, set it to null
                $email->setRelation('relatedModel', null);
            }
        }
        
        return view('admin.email-tracking.show', compact('email'));
    }

    /**
     * Resend email
     */
    public function resend(Email $email): JsonResponse
    {
        try {
            // Create a new email record for resending
            $newEmail = $this->emailTrackingService->trackEmail(
                $email->recipient_email,
                $email->subject,
                $email->body,
                $email->email_type,
                $email->user,
                $email->conference,
                $email->related_model_type,
                $email->related_model_id,
                $email->template_name,
                $email->metadata
            );

            // Send the email
            $this->emailTrackingService->sendTrackedEmail(
                $email->recipient_email,
                $email->subject,
                $email->body,
                $email->email_type,
                $email->user,
                $email->conference,
                $email->related_model_type,
                $email->related_model_id,
                $email->template_name,
                $email->metadata
            );

            return response()->json([
                'success' => true,
                'message' => 'Email resent successfully',
                'new_email_id' => $newEmail->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete email record
     */
    public function destroy(Email $email): JsonResponse
    {
        try {
            $email->delete();

            return response()->json([
                'success' => true,
                'message' => 'Email record deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete email record: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export emails to CSV
     */
    public function export(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        $days = $request->get('days', 30);
        $type = $request->get('type');

        $query = Email::recent($days)->with(['user', 'conference']);

        if ($conferenceId) {
            $query->byConference($conferenceId);
        }

        if ($type) {
            $query->byType($type);
        }

        $emails = $query->orderBy('sent_at', 'desc')->get();

        $filename = 'emails_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($emails) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Recipient Email',
                'Recipient Name',
                'Subject',
                'Type',
                'Status',
                'Sent At',
                'Delivered At',
                'Opened At',
                'Conference',
                'Sender',
                'Error Message'
            ]);

            // CSV data
            foreach ($emails as $email) {
                fputcsv($file, [
                    $email->id,
                    $email->recipient_email,
                    $email->recipient_name,
                    $email->subject,
                    $email->email_type,
                    $email->status,
                    $email->sent_at?->format('Y-m-d H:i:s'),
                    $email->delivered_at?->format('Y-m-d H:i:s'),
                    $email->opened_at?->format('Y-m-d H:i:s'),
                    $email->conference?->name,
                    $email->user?->first_name . ' ' . $email->user?->last_name,
                    $email->error_message,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clean up old email records
     */
    public function cleanup(Request $request): JsonResponse
    {
        $days = $request->get('days', 90);
        
        try {
            $deleted = $this->emailTrackingService->cleanupOldEmails($days);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deleted} old email records",
                'deleted_count' => $deleted,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup old emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get participants with email conversations
     */
    public function getParticipantsWithEmails(Request $request): JsonResponse
    {
        $conferenceId = $request->get('conference_id');
        $participants = $this->emailTrackingService->getParticipantsWithEmails($conferenceId);
        
        $formattedParticipants = $participants->map(function($participant) {
            return [
                'id' => $participant->id,
                'email' => $participant->user->email,
                'name' => $participant->user->first_name . ' ' . $participant->user->last_name,
                'conference' => $participant->conference->name ?? 'N/A',
                'participant_type' => $participant->participantType->name ?? 'N/A',
            ];
        });
        
        return response()->json($formattedParticipants);
    }

    /**
     * Get conversations for a participant
     */
    public function getParticipantConversations(Request $request): View
    {
        $participantEmail = $request->get('participant_email');
        $conferenceId = $request->get('conference_id');
        
        if (!$participantEmail) {
            return view('admin.email-tracking.conversations', [
                'conversations' => collect(),
                'participantEmail' => null,
                'error' => 'No participant selected'
            ]);
        }
        
        $conversations = $this->emailTrackingService->getParticipantConversations($participantEmail, $conferenceId);
        $stats = $this->emailTrackingService->getParticipantConversationStats($participantEmail, $conferenceId);
        
        return view('admin.email-tracking.conversations', compact(
            'conversations',
            'participantEmail',
            'stats'
        ));
    }

    /**
     * Get specific conversation thread
     */
    public function getConversationThread(Request $request, string $threadId): View
    {
        $thread = $this->emailTrackingService->getConversationThread($threadId);
        
        if ($thread->isEmpty()) {
            abort(404, 'Conversation thread not found');
        }
        
        return view('admin.email-tracking.thread', compact('thread', 'threadId'));
    }

    /**
     * Search emails within participant conversations
     */
    public function searchParticipantEmails(Request $request): JsonResponse
    {
        $participantEmail = $request->get('participant_email');
        $searchTerm = $request->get('search_term');
        $conferenceId = $request->get('conference_id');
        
        if (!$participantEmail || !$searchTerm) {
            return response()->json([
                'success' => false,
                'message' => 'Participant email and search term are required'
            ], 400);
        }
        
        $emails = $this->emailTrackingService->searchParticipantEmails($participantEmail, $searchTerm, $conferenceId);
        
        return response()->json([
            'success' => true,
            'emails' => $emails->map(function($email) {
                return [
                    'id' => $email->id,
                    'subject' => $email->subject,
                    'body_preview' => \Str::limit(strip_tags($email->body), 100),
                    'created_at' => $email->created_at->format('M d, Y H:i'),
                    'direction' => $email->direction,
                    'thread_id' => $email->thread_id,
                ];
            })
        ]);
    }
}
