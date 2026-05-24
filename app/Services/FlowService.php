<?php

namespace App\Services;

use App\Helpers\FlowLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('flow.api_key');
        $this->secretKey = config('flow.secret_key');
        $this->baseUrl = config('flow.url');

        FlowLogger::log('INIT', [
            'apiKey' => substr($this->apiKey, 0, 8) . '...',
            'secretKey_len' => strlen($this->secretKey),
            'baseUrl' => $this->baseUrl,
        ]);
    }

    public function createPayment(array $params): array
    {
        FlowLogger::log('PAYMENT_CREATE_START', [
            'order_number' => $params['order_number'],
            'subject' => $params['subject'],
            'amount' => $params['amount'],
            'email' => $params['email'],
        ]);

        $data = [
            'apiKey' => $this->apiKey,
            'commerceOrder' => $params['order_number'],
            'subject' => $params['subject'],
            'currency' => 'CLP',
            'amount' => $params['amount'],
            'email' => $params['email'],
            'urlConfirmation' => config('flow.confirmation_url'),
            'urlReturn' => config('flow.return_url'),
            'optional' => json_encode(['orderId' => $params['order_id']]),
        ];

        $data['s'] = $this->generateSignature($data, 'PAYMENT_CREATE');

        FlowLogger::log('PAYMENT_CREATE_DATA', [
            'data_sent' => $data,
            'signature' => $data['s'],
        ]);

        $fullUrl = $this->baseUrl . '/payment/create';

        $response = Http::withOptions(['verify' => config('flow.ssl_verify')])
            ->asForm()
            ->post($fullUrl, $data);

        FlowLogger::log('PAYMENT_CREATE_RESPONSE', [
            'http_status' => $response->status(),
            'body_raw' => $response->body(),
            'body_json' => $response->json(),
            'successful' => $response->successful(),
            'failed' => $response->failed(),
        ]);

        Log::info('Flow.cl payment response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            $errorMsg = 'Error al crear el pago en Flow.cl: ' . $response->body();
            FlowLogger::log('PAYMENT_CREATE_FAILED', $errorMsg);
            throw new \Exception($errorMsg);
        }

        FlowLogger::log('PAYMENT_CREATE_OK', $response->json());
        return $response->json();
    }

    public function verifyPayment(string $token): array
    {
        FlowLogger::log('VERIFY_PAYMENT_START', ['token' => $token]);

        $data = [
            'apiKey' => $this->apiKey,
            'token' => $token,
        ];

        $data['s'] = $this->generateSignature($data, 'VERIFY_PAYMENT');

        FlowLogger::log('VERIFY_PAYMENT_DATA', [
            'url' => $this->baseUrl . '/payment/getStatus',
            'query_params' => $data,
        ]);

        $response = Http::withOptions(['verify' => config('flow.ssl_verify')])
            ->get($this->baseUrl . '/payment/getStatus', $data);

        FlowLogger::log('VERIFY_PAYMENT_RESPONSE', [
            'http_status' => $response->status(),
            'body_raw' => $response->body(),
            'body_json' => $response->json(),
            'successful' => $response->successful(),
            'failed' => $response->failed(),
        ]);

        Log::info('Flow.cl verification response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            FlowLogger::log('VERIFY_PAYMENT_FAILED', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Error al verificar el pago en Flow.cl: ' . $response->body());
        }

        FlowLogger::log('VERIFY_PAYMENT_OK', $response->json());
        return $response->json();
    }

    public function generateSignature(array $data, string $context = 'GENERAL'): string
    {
        unset($data['s']);
        ksort($data);

        $toSign = '';
        foreach ($data as $key => $value) {
            $toSign .= $key . $value;
        }

        $signature = hash_hmac('sha256', $toSign, $this->secretKey);

        FlowLogger::log('SIGNATURE_' . $context, [
            'keys_sorted' => array_keys($data),
            'string_to_sign' => substr($toSign, 0, 200) . (strlen($toSign) > 200 ? '...[truncated]' : ''),
            'string_length' => strlen($toSign),
            'secret_key_first_chars' => substr($this->secretKey, 0, 4) . '...',
            'signature' => $signature,
        ]);

        return $signature;
    }
}
