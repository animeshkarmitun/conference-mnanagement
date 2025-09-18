<?php

namespace App\Services;

use App\Models\Email;
use App\Models\User;
use App\Models\Conference;
use App\Models\EmailThread;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmailTrackingService
{
    /**
     * Track an email before sending
     */
    public function trackEmail(
        string $recipientEmail,
        string $subject,
        string $body,
        string $emailType = Email::TYPE_GENERAL,
        ?User $sender = null,
        ?Conference $conference = null,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        ?string $templateName = null,
        array $metadata = []
    ): Email {
        $recipientName = $this->getRecipientName($recipientEmail);
        
        $email = Email::create([
            'user_id' => $sender?->id,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'body' => $body,
            'status' => Email::STATUS_PENDING,
            'email_type' => $emailType,
            'related_model_type' => $relatedModelType,
            'related_model_id' => $relatedModelId,
            'template_name' => $templateName,
            'conference_id' => $conference?->id,
            'metadata' => $metadata,
        ]);

        Log::info('Email tracked for sending', [
            'email_id' => $email->id,
            'recipient' => $recipientEmail,
            'type' => $emailType,
            'conference_id' => $conference?->id,
        ]);

        return $email;
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(Email $email, ?string $messageId = null): void
    {
        $email->update([
            'status' => Email::STATUS_SENT,
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);

        Log::info('Email marked as sent', [
            'email_id' => $email->id,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Mark email as delivered
     */
    public function markAsDelivered(Email $email): void
    {
        $email->markAsDelivered();

        Log::info('Email marked as delivered', [
            'email_id' => $email->id,
        ]);
    }

    /**
     * Mark email as opened
     */
    public function markAsOpened(Email $email): void
    {
        $email->markAsOpened();

        Log::info('Email marked as opened', [
            'email_id' => $email->id,
        ]);
    }

    /**
     * Mark email as bounced
     */
    public function markAsBounced(Email $email, string $errorMessage = null): void
    {
        $email->markAsBounced($errorMessage);

        Log::warning('Email marked as bounced', [
            'email_id' => $email->id,
            'error' => $errorMessage,
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(Email $email, string $errorMessage): void
    {
        $email->markAsFailed($errorMessage);

        Log::error('Email marked as failed', [
            'email_id' => $email->id,
            'error' => $errorMessage,
        ]);
    }

    /**
     * Send email with tracking
     */
    public function sendTrackedEmail(
        string $recipientEmail,
        string $subject,
        string $body,
        string $emailType = Email::TYPE_GENERAL,
        ?User $sender = null,
        ?Conference $conference = null,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        ?string $templateName = null,
        array $metadata = [],
        ?Mailable $mailable = null
    ): Email {
        // Track the email first
        $email = $this->trackEmail(
            $recipientEmail,
            $subject,
            $body,
            $emailType,
            $sender,
            $conference,
            $relatedModelType,
            $relatedModelId,
            $templateName,
            $metadata
        );

        try {
            // Send the email
            if ($mailable) {
                Mail::to($recipientEmail)->send($mailable);
            } else {
                Mail::raw($body, function ($message) use ($recipientEmail, $subject) {
                    $message->to($recipientEmail)->subject($subject);
                });
            }

            // Mark as sent
            $this->markAsSent($email);

        } catch (\Exception $e) {
            // Mark as failed
            $this->markAsFailed($email, $e->getMessage());
            
            throw $e;
        }

        return $email;
    }

    /**
     * Get email statistics
     */
    public function getEmailStats(?int $conferenceId = null, int $days = 30): array
    {
        $query = Email::recent($days);
        
        if ($conferenceId) {
            $query->byConference($conferenceId);
        }

        $total = $query->count();
        $sent = $query->clone()->sent()->count();
        $delivered = $query->clone()->delivered()->count();
        $opened = $query->clone()->opened()->count();
        $bounced = $query->clone()->bounced()->count();
        $failed = $query->clone()->failed()->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'delivered' => $delivered,
            'opened' => $opened,
            'bounced' => $bounced,
            'failed' => $failed,
            'delivery_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
            'open_rate' => $delivered > 0 ? round(($opened / $delivered) * 100, 2) : 0,
            'bounce_rate' => $total > 0 ? round(($bounced / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get email statistics by type
     */
    public function getEmailStatsByType(?int $conferenceId = null, int $days = 30): array
    {
        $query = Email::recent($days);
        
        if ($conferenceId) {
            $query->byConference($conferenceId);
        }

        $stats = [];
        $types = Email::getTypeOptions();

        foreach ($types as $type => $label) {
            $typeQuery = $query->clone()->byType($type);
            $total = $typeQuery->count();
            $sent = $typeQuery->clone()->sent()->count();
            $delivered = $typeQuery->clone()->delivered()->count();
            $opened = $typeQuery->clone()->opened()->count();

            $stats[$type] = [
                'label' => $label,
                'total' => $total,
                'sent' => $sent,
                'delivered' => $delivered,
                'opened' => $opened,
                'delivery_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
                'open_rate' => $delivered > 0 ? round(($opened / $delivered) * 100, 2) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Get recent emails with optional filters
     */
    public function getRecentEmails(
        int $limit = 50,
        ?int $conferenceId = null,
        ?int $userId = null,
        ?string $recipientEmail = null,
        ?string $roleName = null,
        ?string $emailType = null
    ): LengthAwarePaginator {
        $query = Email::with(['user.roles', 'conference'])
            ->orderBy('sent_at', 'desc');

        if ($conferenceId) {
            $query->byConference($conferenceId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($recipientEmail) {
            $needle = strtolower(trim($recipientEmail));
            $query->where(function ($q) use ($needle) {
                $q->whereRaw('LOWER(recipient_email) LIKE ?', ['%' . $needle . '%'])
                  ->orWhereRaw('LOWER(recipient_name) LIKE ?', ['%' . $needle . '%']);
            });
        }

        if ($roleName) {
            $query->whereHas('user.roles', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        if ($emailType) {
            $query->byType($emailType);
        }

        return $query->paginate($limit);
    }

    /**
     * Get recipient name from email
     */
    private function getRecipientName(string $email): ?string
    {
        // Try to find user by email
        $user = User::where('email', $email)->first();
        if ($user) {
            return $user->first_name . ' ' . $user->last_name;
        }

        // Extract name from email if possible
        $localPart = explode('@', $email)[0];
        $name = str_replace(['.', '_', '-'], ' ', $localPart);
        return ucwords($name);
    }

    /**
     * Clean up old email records
     */
    public function cleanupOldEmails(int $days = 90): int
    {
        $deleted = Email::where('sent_at', '<', now()->subDays($days))->delete();
        
        Log::info('Cleaned up old email records', [
            'deleted_count' => $deleted,
            'older_than_days' => $days,
        ]);

        return $deleted;
    }

    /**
     * Get participants who have email conversations
     */
    public function getParticipantsWithEmails(?int $conferenceId = null): Collection
    {
        $query = Participant::with(['user', 'conference'])
            ->whereHas('user', function($q) {
                $q->whereHas('emails', function($emailQuery) {
                    $emailQuery->where('direction', Email::DIRECTION_OUTGOING);
                });
            });
        
        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get conversations for a participant
     */
    public function getParticipantConversations(string $participantEmail, ?int $conferenceId = null): Collection
    {
        $query = Email::with(['user', 'emailThread'])
            ->byParticipant($participantEmail)
            ->orderBy('created_at', 'desc');

        if ($conferenceId) {
            $query->byConference($conferenceId);
        }

        $emails = $query->get();
        
        // Group emails by thread_id, creating threads for emails without one
        $conversations = collect();
        
        foreach ($emails as $email) {
            $threadId = $email->thread_id;
            
            if (!$threadId) {
                // Create a thread for this email if it doesn't have one
                $threadId = $this->createThreadForEmail($email);
            }
            
            if (!$conversations->has($threadId)) {
                $conversations->put($threadId, collect());
            }
            
            $conversations->get($threadId)->push($email);
        }
        
        return $conversations;
    }

    /**
     * Get specific conversation thread
     */
    public function getConversationThread(string $threadId): Collection
    {
        return Email::with(['user', 'emailThread'])
            ->byThread($threadId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Create a thread for an email that doesn't have one
     */
    private function createThreadForEmail(Email $email): string
    {
        $thread = EmailThread::createThread(
            $email->subject,
            $email->isOutgoing() ? $email->recipient_email : $email->sender_email,
            $email->conference_id
        );
        
        // Update the email with the thread ID
        $email->update(['thread_id' => $thread->id]);
        
        return $thread->id;
    }

    /**
     * Get conversation statistics for a participant
     */
    public function getParticipantConversationStats(string $participantEmail, ?int $conferenceId = null): array
    {
        $query = Email::byParticipant($participantEmail);
        
        if ($conferenceId) {
            $query->byConference($conferenceId);
        }
        
        $totalEmails = $query->count();
        $outgoingEmails = $query->clone()->outgoing()->count();
        $incomingEmails = $query->clone()->incoming()->count();
        $conversations = $query->clone()->whereNotNull('thread_id')->distinct('thread_id')->count();
        
        return [
            'total_emails' => $totalEmails,
            'outgoing_emails' => $outgoingEmails,
            'incoming_emails' => $incomingEmails,
            'conversations' => $conversations,
            'avg_emails_per_conversation' => $conversations > 0 ? round($totalEmails / $conversations, 2) : 0,
        ];
    }

    /**
     * Create a new conversation thread
     */
    public function createConversationThread(
        string $subject,
        string $participantEmail,
        ?int $conferenceId = null
    ): EmailThread {
        return EmailThread::createThread($subject, $participantEmail, $conferenceId);
    }

    /**
     * Add email to existing thread
     */
    public function addEmailToThread(
        string $threadId,
        string $recipientEmail,
        string $subject,
        string $body,
        string $emailType = Email::TYPE_GENERAL,
        ?User $sender = null,
        ?Conference $conference = null,
        array $metadata = []
    ): Email {
        $thread = EmailThread::findOrFail($threadId);
        
        $email = Email::create([
            'user_id' => $sender?->id,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $this->getRecipientName($recipientEmail),
            'subject' => $subject,
            'body' => $body,
            'status' => Email::STATUS_PENDING,
            'email_type' => $emailType,
            'conference_id' => $conference?->id,
            'metadata' => $metadata,
            'direction' => Email::DIRECTION_OUTGOING,
            'thread_id' => $threadId,
        ]);
        
        // Update thread's last activity
        $thread->updateLastActivity();
        
        Log::info('Email added to thread', [
            'email_id' => $email->id,
            'thread_id' => $threadId,
            'recipient' => $recipientEmail,
        ]);
        
        return $email;
    }

    /**
     * Search emails within participant conversations
     */
    public function searchParticipantEmails(
        string $participantEmail,
        string $searchTerm,
        ?int $conferenceId = null
    ): Collection {
        $query = Email::byParticipant($participantEmail)
            ->where(function($q) use ($searchTerm) {
                $q->where('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%");
            });
        
        if ($conferenceId) {
            $query->byConference($conferenceId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
}
