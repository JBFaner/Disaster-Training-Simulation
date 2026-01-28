<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS via configured provider
     */
    public function send(string $to, string $message): bool
    {
        $provider = config('services.sms.provider', 'twilio');

        return match ($provider) {
            'twilio' => $this->sendViaTwilio($to, $message),
            'vonage' => $this->sendViaVonage($to, $message),
            'log' => $this->sendViaLog($to, $message),
            default => $this->sendViaLog($to, $message),
        };
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $to, string $message): bool
    {
        $accountSid = config('services.sms.twilio.account_sid');
        $authToken = config('services.sms.twilio.auth_token');
        $from = config('services.sms.twilio.from');

        if (!$accountSid || !$authToken || !$from) {
            Log::warning('Twilio credentials not configured. Falling back to log.');
            return $this->sendViaLog($to, $message);
        }

        try {
            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $from,
                    'To' => $to,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully to {$to} via Twilio");
                return true;
            }

            Log::error("Failed to send SMS via Twilio: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Twilio SMS error: " . $e->getMessage());
            return $this->sendViaLog($to, $message);
        }
    }

    /**
     * Send SMS via Vonage (formerly Nexmo)
     */
    protected function sendViaVonage(string $to, string $message): bool
    {
        $apiKey = config('services.sms.vonage.api_key');
        $apiSecret = config('services.sms.vonage.api_secret');
        $from = config('services.sms.vonage.from');

        if (!$apiKey || !$apiSecret || !$from) {
            Log::warning('Vonage credentials not configured. Falling back to log.');
            return $this->sendViaLog($to, $message);
        }

        try {
            $response = Http::withBasicAuth($apiKey, $apiSecret)
                ->post('https://rest.nexmo.com/sms/json', [
                    'from' => $from,
                    'to' => $to,
                    'text' => $message,
                ]);

            if ($response->successful() && $response->json('messages.0.status') === '0') {
                Log::info("SMS sent successfully to {$to} via Vonage");
                return true;
            }

            Log::error("Failed to send SMS via Vonage: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Vonage SMS error: " . $e->getMessage());
            return $this->sendViaLog($to, $message);
        }
    }

    /**
     * Log SMS (for development/testing)
     */
    protected function sendViaLog(string $to, string $message): bool
    {
        Log::info("SMS (Log Mode) to {$to}: {$message}");
        return true;
    }

    /**
     * Send OTP code
     */
    public function sendOtp(string $to, string $otp): bool
    {
        $message = "Your verification code is: {$otp}\n\nThis code will expire in 15 minutes.\n\nIf you didn't request this code, please ignore this message.";
        return $this->send($to, $message);
    }
}

