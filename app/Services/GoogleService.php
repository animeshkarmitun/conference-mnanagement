<?php
namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class GoogleService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName(config('google.application_name'));
        $this->client->setScopes(config('google.scopes'));
        $this->client->setClientId(config('google.client_id'));
        $this->client->setClientSecret(config('google.client_secret'));
        $this->client->setRedirectUri(config('google.redirect_uri'));
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        
        // Fix SSL certificate issue for Windows development
        $this->client->setHttpClient(new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification for development
        ]));
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    /**
     * Refresh the access token if it's expired
     */
    public function refreshTokenIfNeeded($token)
    {
        $this->client->setAccessToken($token);
        
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            if ($refreshToken) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                return $newToken;
            } else {
                throw new \Exception('No refresh token available. Please reconnect your Gmail account.');
            }
        }
        
        return $token;
    }

    public function listThreads($userId = 'me', $maxResults = 10, $pageToken = null, $query = null)
    {
        $service = new Gmail($this->client);
        $params = ['maxResults' => $maxResults];
        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }
        if ($query) {
            $params['q'] = $query;
        }
        $results = $service->users_threads->listUsersThreads($userId, $params);
        $threads = [];

        if ($results->getThreads()) {
            foreach ($results->getThreads() as $thread) {
                $threadData = $service->users_threads->get($userId, $thread->getId());
                $threads[] = [
                    'id' => $thread->getId(),
                    'snippet' => $thread->getSnippet(),
                    'messages' => $threadData->getMessages(),
                ];
            }
        }

        return [
            'threads' => $threads,
            'nextPageToken' => $results->getNextPageToken() ?? null,
        ];
    }

    // Helper to extract header value
    public function getHeader($message, $headerName)
    {
        $headers = $message->getPayload()->getHeaders();
        foreach ($headers as $header) {
            if ($header->getName() === $headerName) {
                return $header->getValue();
            }
        }
        return '';
    }

    // Send email
    public function sendEmail($to, $subject, $body, $threadId = null)
    {
        $service = new Gmail($this->client);
        
        // Create email message
        $message = $this->createMessage($to, $subject, $body, $threadId);
        
        // Send the message
        $sentMessage = $service->users_messages->send('me', $message);
        
        return $sentMessage;
    }

    // Send email and return both message and thread info
    public function sendEmailWithThread($to, $subject, $body, $threadId = null, $participantEmail = null)
    {
        $service = new Gmail($this->client);
        
        // If no thread ID provided, try to find existing thread for participant
        if (!$threadId && $participantEmail) {
            $threadId = $this->findExistingThread($participantEmail);
        }
        
        // Create email message
        $message = $this->createMessage($to, $subject, $body, $threadId);
        
        // Send the message
        $sentMessage = $service->users_messages->send('me', $message);
        
        // Get the thread ID from the sent message
        $actualThreadId = $sentMessage->getThreadId();
        
        return [
            'message' => $sentMessage,
            'thread_id' => $actualThreadId,
            'message_id' => $sentMessage->getId()
        ];
    }

    // Find existing thread for participant
    public function findExistingThread($participantEmail, $userId = 'me')
    {
        $service = new Gmail($this->client);
        
        // Search for threads with this participant
        $query = "to:{$participantEmail} OR from:{$participantEmail}";
        $results = $service->users_threads->listUsersThreads($userId, [
            'q' => $query,
            'maxResults' => 1
        ]);
        
        if ($results->getThreads() && count($results->getThreads()) > 0) {
            return $results->getThreads()[0]->getId();
        }
        
        return null;
    }

    // Create or get thread for participant
    public function getOrCreateThreadForParticipant($participantEmail, $subject = null)
    {
        // First try to find existing thread
        $threadId = $this->findExistingThread($participantEmail);
        
        if ($threadId) {
            return $threadId;
        }
        
        // If no existing thread and subject provided, create new thread by sending initial email
        if ($subject) {
            $message = $this->createMessage($participantEmail, $subject, 'Initial conversation thread');
            $service = new Gmail($this->client);
            $sentMessage = $service->users_messages->send('me', $message);
            return $sentMessage->getThreadId();
        }
        
        return null;
    }

    // Create email message
    private function createMessage($to, $subject, $body, $threadId = null)
    {
        $message = new \Google\Service\Gmail\Message();
        
        // Create email headers
        $headers = [
            'To' => $to,
            'Subject' => $subject,
            'Content-Type' => 'text/html; charset=UTF-8',
            'MIME-Version' => '1.0'
        ];

        // If replying to a thread, add thread ID
        if ($threadId) {
            $headers['In-Reply-To'] = $threadId;
            $headers['References'] = $threadId;
        }

        // Create email content
        $emailContent = '';
        foreach ($headers as $key => $value) {
            $emailContent .= "$key: $value\r\n";
        }
        $emailContent .= "\r\n" . $body;

        // Encode the message
        $encodedMessage = base64_encode($emailContent);
        $message->setRaw($encodedMessage);

        return $message;
    }

    // Get thread details for reply
    public function getThread($threadId, $userId = 'me')
    {
        $service = new Gmail($this->client);
        return $service->users_threads->get($userId, $threadId);
    }

    // Extract email address from "Name <email>" format
    public function extractEmailAddress($fromString)
    {
        if (preg_match('/<(.+?)>/', $fromString, $matches)) {
            return $matches[1];
        }
        return $fromString;
    }

    // Get message details
    public function getMessage($messageId, $userId = 'me')
    {
        $service = new Gmail($this->client);
        return $service->users_messages->get($userId, $messageId);
    }

    // Process incoming Gmail message
    public function processIncomingMessage($messageId, $userId = 'me')
    {
        $message = $this->getMessage($messageId, $userId);
        
        $headers = $message->getPayload()->getHeaders();
        $from = '';
        $to = '';
        $subject = '';
        $date = '';
        
        foreach ($headers as $header) {
            switch ($header->getName()) {
                case 'From':
                    $from = $header->getValue();
                    break;
                case 'To':
                    $to = $header->getValue();
                    break;
                case 'Subject':
                    $subject = $header->getValue();
                    break;
                case 'Date':
                    $date = $header->getValue();
                    break;
            }
        }
        
        // Extract body content
        $body = $this->extractMessageBody($message->getPayload());
        
        return [
            'message_id' => $messageId,
            'thread_id' => $message->getThreadId(),
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'date' => $date,
            'snippet' => $message->getSnippet(),
            'internal_date' => $message->getInternalDate()
        ];
    }

    // Extract message body from payload
    private function extractMessageBody($payload)
    {
        $body = '';
        
        if ($payload->getBody() && $payload->getBody()->getData()) {
            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload->getBody()->getData()));
        } elseif ($payload->getParts()) {
            foreach ($payload->getParts() as $part) {
                if ($part->getMimeType() === 'text/plain' || $part->getMimeType() === 'text/html') {
                    if ($part->getBody() && $part->getBody()->getData()) {
                        $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                        break;
                    }
                }
            }
        }
        
        return $body;
    }

    // Search for messages by participant email
    public function searchMessagesByParticipant($participantEmail, $userId = 'me', $maxResults = 50)
    {
        $service = new Gmail($this->client);
        
        $query = "to:{$participantEmail} OR from:{$participantEmail}";
        $results = $service->users_messages->listUsersMessages($userId, [
            'q' => $query,
            'maxResults' => $maxResults
        ]);
        
        $messages = [];
        if ($results->getMessages()) {
            foreach ($results->getMessages() as $message) {
                $messages[] = $this->processIncomingMessage($message->getId(), $userId);
            }
        }
        
        return $messages;
    }
} 