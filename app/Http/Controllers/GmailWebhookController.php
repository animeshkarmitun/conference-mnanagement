<?php

namespace App\Http\Controllers;

use App\Services\GmailIncomingEmailService;
use App\Services\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GmailWebhookController extends Controller
{
    protected GmailIncomingEmailService $gmailIncomingService;
    protected GoogleService $googleService;

    public function __construct(GmailIncomingEmailService $gmailIncomingService, GoogleService $googleService)
    {
        $this->gmailIncomingService = $gmailIncomingService;
        $this->googleService = $googleService;
    }

    /**
     * Handle Gmail push notifications
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Verify webhook signature if needed
            $this->verifyWebhookSignature($request);

            $data = $request->all();
            Log::info('Gmail webhook received', $data);

            // Process the webhook data
            if (isset($data['message']['data'])) {
                $this->processWebhookNotification($data);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Gmail webhook error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process webhook notification
     */
    private function processWebhookNotification($data)
    {
        // Decode the base64 message data
        $messageData = base64_decode($data['message']['data']);
        $notification = json_decode($messageData, true);

        if (isset($notification['emailAddress']) && isset($notification['historyId'])) {
            // Process new messages
            $this->processNewMessages($notification['emailAddress'], $notification['historyId']);
        }
    }

    /**
     * Process new messages from Gmail
     */
    private function processNewMessages($emailAddress, $historyId)
    {
        try {
            // Get the admin user's Gmail token
            $adminUser = $this->getAdminUserWithGmail();
            if (!$adminUser) {
                Log::warning('No admin user with Gmail token found');
                return;
            }

            $this->googleService->setAccessToken(json_decode($adminUser->google_token, true));

            // Get recent messages
            $messages = $this->getRecentMessages($emailAddress);

            foreach ($messages as $messageId) {
                $this->gmailIncomingService->processIncomingEmail($messageId);
            }

            Log::info('Processed new Gmail messages', [
                'email_address' => $emailAddress,
                'history_id' => $historyId,
                'messages_processed' => count($messages)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process new Gmail messages', [
                'email_address' => $emailAddress,
                'history_id' => $historyId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get recent messages from Gmail
     */
    private function getRecentMessages($userId = 'me', $maxResults = 10)
    {
        $service = new \Google\Service\Gmail($this->googleService->getClient());
        
        $results = $service->users_messages->listUsersMessages($userId, [
            'maxResults' => $maxResults,
            'q' => 'is:unread'
        ]);

        $messageIds = [];
        if ($results->getMessages()) {
            foreach ($results->getMessages() as $message) {
                $messageIds[] = $message->getId();
            }
        }

        return $messageIds;
    }

    /**
     * Get admin user with Gmail token
     */
    private function getAdminUserWithGmail()
    {
        return \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'superadmin']);
        })->whereNotNull('google_token')->first();
    }

    /**
     * Verify webhook signature (implement based on your security requirements)
     */
    private function verifyWebhookSignature(Request $request)
    {
        // Implement webhook signature verification
        // This is important for security
        return true;
    }

    /**
     * Manual sync for testing
     */
    public function manualSync(Request $request)
    {
        try {
            $participantEmail = $request->input('participant_email');
            $conferenceId = $request->input('conference_id');

            if (!$participantEmail) {
                return response()->json(['error' => 'Participant email required'], 400);
            }

            $adminUser = $this->getAdminUserWithGmail();
            if (!$adminUser) {
                return response()->json(['error' => 'No admin user with Gmail token found'], 400);
            }

            $this->googleService->setAccessToken(json_decode($adminUser->google_token, true));

            $syncedCount = $this->gmailIncomingService->syncParticipantGmailMessages($participantEmail);

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'participant_email' => $participantEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Manual sync failed', [
                'error' => $e->getMessage(),
                'participant_email' => $request->input('participant_email')
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

