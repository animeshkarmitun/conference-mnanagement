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
} 