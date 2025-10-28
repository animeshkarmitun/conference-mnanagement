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
                try {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    return $newToken;
                } catch (\Exception $e) {
                    throw new \Exception('Failed to refresh token. Please reconnect your Gmail account: ' . $e->getMessage());
                }
            } else {
                throw new \Exception('No refresh token available. Please reconnect your Gmail account.');
            }
        }
        
        return $token;
    }

    /**
     * Check if the current token is valid and refresh if needed
     */
    public function ensureValidToken($token)
    {
        try {
            $this->client->setAccessToken($token);
            
            // Check if token is expired
            if ($this->client->isAccessTokenExpired()) {
                \Log::info('Gmail token expired, attempting refresh...');
                return $this->refreshTokenIfNeeded($token);
            }
            
            return $token;
        } catch (\Exception $e) {
            \Log::error('Token validation failed: ' . $e->getMessage());
            throw new \Exception('Gmail authentication failed. Please reconnect your account.');
        }
    }

    /**
     * Test the current token by making a simple API call
     */
    public function testToken($token)
    {
        try {
            $this->client->setAccessToken($token);
            $service = new Gmail($this->client);
            
            // Make a simple API call to test the token
            $profile = $service->users->getProfile('me');
            return true;
        } catch (\Exception $e) {
            \Log::error('Token test failed: ' . $e->getMessage());
            return false;
        }
    }

    public function listThreads($userId = 'me', $maxResults = 10, $pageToken = null, $query = null)
    {
        try {
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
                    // Fetch thread with full format to get complete message bodies
                    $threadData = $service->users_threads->get($userId, $thread->getId(), ['format' => 'full']);
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
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() == 401) {
                throw new \Exception('Gmail authentication expired. Please reconnect your Gmail account.');
            }
            throw new \Exception('Gmail API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch Gmail threads: ' . $e->getMessage());
        }
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

        // Encode the message using URL-safe base64 encoding (required by Gmail API)
        // Gmail API requires base64url encoding: replace + with -, / with _, and remove padding =
        $encodedMessage = rtrim(strtr(base64_encode($emailContent), '+/', '-_'), '=');
        $message->setRaw($encodedMessage);

        return $message;
    }

    // Get thread details for reply
    public function getThread($threadId, $userId = 'me')
    {
        $service = new Gmail($this->client);
        // Fetch with full format to get complete message bodies
        return $service->users_threads->get($userId, $threadId, ['format' => 'full']);
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

    // Extract message body from payload (public for view access)
    public function extractMessageBody($payload)
    {
        $body = '';
        
        // Try to get body directly
        if ($payload->getBody() && $payload->getBody()->getData()) {
            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload->getBody()->getData()));
        } 
        // If no direct body, look in parts (multipart email)
        elseif ($payload->getParts()) {
            // First, try to get HTML version (more complete)
            foreach ($payload->getParts() as $part) {
                if ($part->getMimeType() === 'text/html') {
                    if ($part->getBody() && $part->getBody()->getData()) {
                        $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                        break;
                    }
                    // Check nested parts for HTML
                    if ($part->getParts()) {
                        $body = $this->extractMessageBody($part);
                        if (!empty($body)) break;
                    }
                }
            }
            
            // If no HTML found, try plain text
            if (empty($body)) {
                foreach ($payload->getParts() as $part) {
                    if ($part->getMimeType() === 'text/plain') {
                        if ($part->getBody() && $part->getBody()->getData()) {
                            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                            break;
                        }
                        // Check nested parts for plain text
                        if ($part->getParts()) {
                            $body = $this->extractMessageBody($part);
                            if (!empty($body)) break;
                        }
                    }
                }
            }
        }
        
        return $body;
    }

    // Get full message body from a message object
    public function getMessageBody($message)
    {
        if (!$message || !$message->getPayload()) {
            return '';
        }
        
        return $this->extractMessageBody($message->getPayload());
    }

    // Format HTML email body to readable plain text with proper line breaks
    public function formatEmailBody($htmlBody)
    {
        if (empty($htmlBody)) {
            return '';
        }

        // Check if content is HTML
        $isHtml = preg_match('/<(?:html|body|div|p|br)/i', $htmlBody);
        
        if ($isHtml) {
            // FIRST: Remove style tags and their contents (CSS)
            $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $htmlBody);
            
            // Remove script tags and their contents (JavaScript)
            $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
            
            // Remove head section entirely (contains meta, title, etc.)
            $text = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $text);
            
            // Convert HTML to formatted text while preserving structure
            // Convert common block elements to line breaks
            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
            $text = preg_replace('/<\/p>/i', "\n\n", $text);
            $text = preg_replace('/<\/div>/i', "\n", $text);
            $text = preg_replace('/<\/h[1-6]>/i', "\n\n", $text);
            $text = preg_replace('/<\/li>/i', "\n", $text);
            $text = preg_replace('/<\/tr>/i', "\n", $text);
            $text = preg_replace('/<\/blockquote>/i', "\n\n", $text);
            $text = preg_replace('/<hr\s*\/?>/i', "\n---\n", $text);
            
            // Strip all remaining HTML tags
            $text = strip_tags($text);
            
            // Remove lines that contain only whitespace
            $text = preg_replace('/^[ \t]+$/m', '', $text);
        } else {
            $text = $htmlBody;
        }
        
        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove any remaining whitespace-only lines
        $text = preg_replace('/^[ \t]+$/m', '', $text);
        
        // Clean up excessive blank lines (reduce multiple blank lines to max 1)
        $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Remove excessive spaces (multiple spaces to single space)
        $text = preg_replace('/ {2,}/', ' ', $text);
        
        // Trim each line
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $text = implode("\n", $lines);
        
        // Remove blank lines at start and end, and reduce multiple consecutive blank lines
        $text = trim($text);
        
        return $text;
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