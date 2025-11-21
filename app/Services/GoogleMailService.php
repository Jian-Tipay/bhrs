<?php
namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

class GoogleMailService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');

        // Set the token if it exists
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);

            // Refresh the token if expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                }
            }
        }

        $this->service = new Gmail($this->client);
    }

    /**
     * Send email using Gmail API
     */
    public function sendEmail($to, $subject, $body, $toName = '')
    {
        try {
            $message = $this->createMessage($to, $subject, $body, $toName);
            $result = $this->service->users_messages->send('me', $message);
            
            Log::info('Email sent successfully via Gmail API', [
                'to' => $to,
                'subject' => $subject,
                'message_id' => $result->getId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email via Gmail API', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Create email message
     */
    protected function createMessage($to, $subject, $body, $toName = '')
    {
        $fromEmail = config('mail.from.address');
        $fromName = config('mail.from.name');

        $toHeader = $toName ? "{$toName} <{$to}>" : $to;

        $rawMessage = "From: {$fromName} <{$fromEmail}>\r\n";
        $rawMessage .= "To: {$toHeader}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessage .= "\r\n";
        $rawMessage .= $body;

        $message = new Message();
        $message->setRaw($this->base64UrlEncode($rawMessage));

        return $message;
    }

    /**
     * Base64 URL encode
     */
    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Get authorization URL for first-time setup
     */
    public function getAuthorizationUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback and save token
     */
    public function handleCallback($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (array_key_exists('error', $accessToken)) {
            throw new \Exception(join(', ', $accessToken));
        }

        $tokenPath = storage_path('app/google/token.json');
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($accessToken));

        return true;
    }
}