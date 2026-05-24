<?php

namespace App\Services;

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
    }

    public function createPayment(array $params): array
    {
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

        $data['s'] = $this->generateSignature($data);

        Log::info('Flow.cl payment request', [
            'url' => $this->baseUrl . '/payment/create',
            'commerceOrder' => $params['order_number'],
            'amount' => $params['amount'],
        ]);

        $response = Http::withOptions(['verify' => config('flow.ssl_verify')])
            ->asForm()
            ->post($this->baseUrl . '/payment/create', $data);

        Log::info('Flow.cl payment response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            Log::error('Flow.cl payment creation failed', [
                'response' => $response->body(),
                'order' => $params['order_number'],
            ]);
            throw new \Exception('Error al crear el pago en Flow.cl: ' . $response->body());
        }

        return $response->json();
    }

    public function verifyPayment(string $token): array
    {
        $data = [
            'apiKey' => $this->apiKey,
            'token' => $token,
        ];

        $data['s'] = $this->generateSignature($data);

        Log::info('Flow.cl verification request', ['token' => $token]);

        $response = Http::withOptions(['verify' => config('flow.ssl_verify')])
            ->asForm()
            ->post($this->baseUrl . '/payment/getStatus', $data);

        Log::info('Flow.cl verification response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            Log::error('Flow.cl payment verification failed', [
                'token' => $token,
                'response' => $response->body(),
            ]);
            throw new \Exception('Error al verificar el pago en Flow.cl');
        }

        return $response->json();
    }

    public function generateSignature(array $data): string
    {
        unset($data['s']);
        ksort($data);
        $stringToSign = http_build_query($data);
        return hash_hmac('sha256', $stringToSign, $this->secretKey);
    }
}
