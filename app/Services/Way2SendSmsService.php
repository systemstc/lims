<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Way2SendSmsService
{
    /**
     * Send an OTP message to the specified mobile number.
     *
     * @param string $mobileNumber
     * @param string|int $otp
     * @param string|null $customTemplateId
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function sendOtp(string $mobileNumber, $otp, ?string $customTemplateId = null): array
    {
        $template = config('services.way2send.otp_message') ?: env(
            'WAY2SEND_OTP_MESSAGE',
            'Use OTP :otp for TC-LIMS login. Valid for 5 mins. Keep it secure. -Textiles Committee'
        );

        // Replace all possible OTP placeholder patterns
        $message = str_replace(
            [':otp', '{#num#}', '{#var#}', '{otp}', '{OTP}', ':code'],
            (string) $otp,
            $template
        );

        $templateId = $customTemplateId ?: (config('services.way2send.template_id') ?: env('WAY2SEND_TEMPLATE_ID', '1177179144024649608'));

        return $this->sendSms($mobileNumber, $message, $templateId);
    }

    /**
     * Send a single SMS via Way2Send CPaaS Gateway.
     *
     * @param string $mobileNumber
     * @param string $message
     * @param string|null $templateId
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function sendSms(string $mobileNumber, string $message, ?string $templateId = null): array
    {
        $enabled = config('services.way2send.enabled', true);
        if (!$enabled) {
            Log::info("Way2Send SMS disabled in configuration. Simulated sending to {$mobileNumber}: {$message}");
            return [
                'success' => true,
                'message' => 'SMS Gateway is disabled (simulation mode).',
                'simulated' => true
            ];
        }

        $apiUrl = config('services.way2send.api_url') ?: env('WAY2SEND_API_URL', 'https://cpaas.way2send.in/api/sendsms');
        $apiKey = config('services.way2send.api_key') ?: env('WAY2SEND_API_KEY');
        $senderId = config('services.way2send.sender_id') ?: env('WAY2SEND_SENDER_ID', 'TEXCOM');
        $peId = config('services.way2send.pe_id') ?: env('WAY2SEND_PE_ID', '1101458770000035349');
        $userId = config('services.way2send.user_id') ?: env('WAY2SEND_USER_ID', '1101458770000035349');
        $userPassword = config('services.way2send.user_password') ?: env('WAY2SEND_USER_PASSWORD');
        $verifySsl = (bool) (config('services.way2send.verify_ssl') ?? env('WAY2SEND_VERIFY_SSL', false));
        $dltTemplateId = $templateId ?: (config('services.way2send.template_id') ?: env('WAY2SEND_TEMPLATE_ID', '1177179144024649608'));

        // Sanitize 10-digit mobile number
        $cleanMobile = preg_replace('/[^0-9]/', '', $mobileNumber);
        if (strlen($cleanMobile) === 12 && str_starts_with($cleanMobile, '91')) {
            $cleanMobile = substr($cleanMobile, 2);
        } elseif (strlen($cleanMobile) === 11 && str_starts_with($cleanMobile, '0')) {
            $cleanMobile = substr($cleanMobile, 1);
        }

        if (strlen($cleanMobile) !== 10) {
            return [
                'success' => false,
                'message' => "Invalid 10-digit Indian mobile number: '{$mobileNumber}'",
            ];
        }

        // Build payload with exact Way2Send CPaaS parameter names
        $payload = [
            'apikey'          => $apiKey,
            'mobiles'         => $cleanMobile,
            'sms'             => $message,
            'senderid'        => $senderId,
            'entityid'        => $peId,
            'tempid'          => $dltTemplateId,

            // Keep aliases for safety
            'api_key'         => $apiKey,
            'mobile'          => $cleanMobile,
            'mobileno'        => $cleanMobile,
            'message'         => $message,
            'templateid'      => $dltTemplateId,
            'peid'            => $peId,
        ];

        if (!empty($userId)) {
            $payload['userid']  = $userId;
            $payload['user_id'] = $userId;
        }

        if (!empty($userPassword)) {
            $payload['password'] = $userPassword;
        }

        try {
            $headers = [
                'apikey'        => $apiKey,
                'api-key'       => $apiKey,
                'x-api-key'     => $apiKey,
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
            ];

            $client = Http::withHeaders($headers)->withOptions([
                'verify'  => $verifySsl,
                'timeout' => 15,
            ]);

            // Append query string in case gateway expects credentials/parameters in URL
            $urlWithQuery = $apiUrl . (str_contains($apiUrl, '?') ? '&' : '?') . http_build_query([
                'apikey'   => $apiKey,
                'mobiles'  => $cleanMobile,
                'sms'      => $message,
                'senderid' => $senderId,
                'entityid' => $peId,
                'tempid'   => $dltTemplateId,
            ]);

            // Attempt 1: Form-urlencoded POST (Standard for Indian CPaaS Gateways)
            $response = $client->asForm()->post($urlWithQuery, $payload);

            // Attempt 2: JSON POST if Form failed
            if (!$response->successful()) {
                Log::debug('Way2Send Form POST failed, trying JSON POST...', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);
                $response = $client->asJson()->post($urlWithQuery, $payload);
            }

            // Attempt 3: GET request if POST returned 400/401/404/405
            if (!$response->successful() && in_array($response->status(), [400, 401, 404, 405])) {
                Log::debug('Way2Send POST failed, trying GET request...', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);
                $response = $client->get($apiUrl, $payload);
            }

            $responseBody = $response->body();
            $statusCode = $response->status();
            $jsonResp = json_decode($responseBody, true);

            $isErrorInBody = false;
            if (is_array($jsonResp)) {
                if (isset($jsonResp['status']) && strtolower($jsonResp['status']) === 'error') {
                    $isErrorInBody = true;
                } elseif (isset($jsonResp['error'])) {
                    $isErrorInBody = true;
                }
            }

            Log::info('Way2Send SMS API Response', [
                'mobile'      => substr($cleanMobile, 0, 2) . '******' . substr($cleanMobile, -2),
                'template_id' => $dltTemplateId,
                'status_code' => $statusCode,
                'response'    => $responseBody,
            ]);

            if ($response->successful() && !$isErrorInBody) {
                return [
                    'success'  => true,
                    'message'  => 'SMS sent successfully.',
                    'response' => $responseBody
                ];
            }

            $errorMsg = 'SMS Gateway responded with error code ' . $statusCode;
            $jsonResp = json_decode($responseBody, true);
            if (isset($jsonResp['error'])) {
                $errorMsg .= ': ' . $jsonResp['error'];
            } elseif (isset($jsonResp['message'])) {
                $errorMsg .= ': ' . $jsonResp['message'];
            }

            if ($statusCode === 401 && (str_ends_with($apiKey, 'XX') || strlen($apiKey) < 16)) {
                $errorMsg .= ' (Please verify that WAY2SEND_API_KEY in your .env contains your complete, unmasked API key)';
            }

            return [
                'success'  => false,
                'message'  => $errorMsg,
                'response' => $responseBody
            ];
        } catch (\Throwable $e) {
            Log::error('Way2Send SMS Gateway Exception: ' . $e->getMessage(), [
                'mobile' => $cleanMobile,
                'trace'  => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'SMS Gateway communication error: ' . $e->getMessage()
            ];
        }
    }
}
