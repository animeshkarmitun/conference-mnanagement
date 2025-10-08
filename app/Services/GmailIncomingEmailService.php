<?php

namespace App\Services;

use App\Models\Email;
use App\Models\Participant;
use App\Models\User;
use App\Models\Conference;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GmailIncomingEmailService
{
    protected GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Process incoming Gmail message and link to participant
     */
    public function processIncomingEmail($messageId, $userId = 'me')
    {
        try {
            // Get message details from Gmail
            $messageData = $this->googleService->processIncomingMessage($messageId, $userId);
            
            // Extract sender email
            $senderEmail = $this->googleService->extractEmailAddress($messageData['from']);
            
            // Find participant by email
            $participant = $this->findParticipantByEmail($senderEmail);
            
            if (!$participant) {
                Log::warning('No participant found for incoming email', [
                    'sender_email' => $senderEmail,
                    'message_id' => $messageId
                ]);
                return null;
            }

            // Check if email already exists
            $existingEmail = Email::where('message_id', $messageId)->first();
            if ($existingEmail) {
                return $existingEmail;
            }

            // Create email record
            $email = Email::create([
                'user_id' => null, // Incoming email, no system user
                'recipient_email' => $this->googleService->extractEmailAddress($messageData['to']),
                'recipient_name' => 'Admin',
                'sender_email' => $senderEmail,
                'sender_name' => $this->extractSenderName($messageData['from']),
                'subject' => $messageData['subject'],
                'body' => $messageData['body'],
                'status' => Email::STATUS_DELIVERED,
                'email_type' => Email::TYPE_GENERAL,
                'direction' => Email::DIRECTION_INCOMING,
                'thread_id' => $messageData['thread_id'],
                'message_id' => $messageId,
                'conference_id' => $participant->conference_id,
                'received_at' => $this->parseEmailDate($messageData['date']),
                'metadata' => [
                    'gmail_message_id' => $messageId,
                    'gmail_thread_id' => $messageData['thread_id'],
                    'participant_id' => $participant->id,
                    'snippet' => $messageData['snippet']
                ]
            ]);

            Log::info('Incoming email processed and linked to participant', [
                'email_id' => $email->id,
                'participant_id' => $participant->id,
                'sender_email' => $senderEmail,
                'thread_id' => $messageData['thread_id']
            ]);

            return $email;

        } catch (\Exception $e) {
            Log::error('Failed to process incoming email', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find participant by email address
     */
    private function findParticipantByEmail($email)
    {
        return Participant::whereHas('user', function($query) use ($email) {
            $query->where('email', $email);
        })->with(['user', 'conference'])->first();
    }

    /**
     * Extract sender name from "Name <email>" format
     */
    private function extractSenderName($fromString)
    {
        if (preg_match('/^(.+?)\s*<.+>$/', $fromString, $matches)) {
            return trim($matches[1], '"');
        }
        return $this->googleService->extractEmailAddress($fromString);
    }

    /**
     * Parse email date string
     */
    private function parseEmailDate($dateString)
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return now();
        }
    }

    /**
     * Sync all Gmail messages for a participant
     */
    public function syncParticipantGmailMessages($participantEmail, $userId = 'me')
    {
        try {
            $messages = $this->googleService->searchMessagesByParticipant($participantEmail, $userId);
            $syncedCount = 0;

            foreach ($messages as $messageData) {
                $existingEmail = Email::where('message_id', $messageData['message_id'])->first();
                
                if (!$existingEmail) {
                    $this->processIncomingEmail($messageData['message_id'], $userId);
                    $syncedCount++;
                }
            }

            Log::info('Synced Gmail messages for participant', [
                'participant_email' => $participantEmail,
                'synced_count' => $syncedCount,
                'total_messages' => count($messages)
            ]);

            return $syncedCount;

        } catch (\Exception $e) {
            Log::error('Failed to sync participant Gmail messages', [
                'participant_email' => $participantEmail,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get conversation thread for participant
     */
    public function getParticipantConversation($participantEmail, $conferenceId = null)
    {
        $query = Email::where(function($q) use ($participantEmail) {
            $q->where('recipient_email', $participantEmail)
              ->orWhere('sender_email', $participantEmail);
        });

        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get all participants with Gmail conversations
     */
    public function getParticipantsWithConversations($conferenceId = null)
    {
        $query = Participant::whereHas('emails', function($q) {
            $q->where('direction', Email::DIRECTION_INCOMING);
        })->with(['user', 'conference', 'emails' => function($q) {
            $q->where('direction', Email::DIRECTION_INCOMING);
        }]);

        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }

        return $query->get();
    }
}

