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

    public function listThreads($userId = 'me')
    {
        $service = new Gmail($this->client);
        $results = $service->users_threads->listUsersThreads($userId, ['maxResults' => 10]);
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

        return $threads;
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
} 