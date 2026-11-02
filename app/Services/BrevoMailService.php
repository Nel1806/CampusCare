<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BrevoMailService
{
    public function send(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $html
    ): array {
        $apiKey = config('services.brevo.api_key');

        if (!$apiKey) {
            throw new RuntimeException('Brevo API key is missing.');
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'api-key' => $apiKey,
            ])
            ->timeout(30)
            ->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => config('mail.from.name'),
                    'email' => config('mail.from.address'),
                ],
                'to' => [[
                    'email' => $recipientEmail,
                    'name' => $recipientName,
                ]],
                'subject' => $subject,
                'htmlContent' => $html,
            ]);

        if ($response->failed()) {
            Log::error('Brevo API email failed.', [
                'status' => $response->status(),
                'response' => $response->json(),
                'recipient' => $recipientEmail,
            ]);

            $response->throw();
        }

        return $response->json();
    }
}
