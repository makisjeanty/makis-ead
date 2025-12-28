<?php

namespace App\Services\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonCashGateway
{
    protected $clientId;
    protected $clientSecret;
    protected $endpoint;
    protected $mode;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.moncash.client_id');
        $this->clientSecret = config('services.moncash.client_secret');
        $this->mode = config('services.moncash.mode', 'sandbox');
        $this->endpoint = config('services.moncash.endpoint');
    }

    /**
     * Get OAuth access token
     */
    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Accept' => 'application/json',
            ])->asForm()->post($this->endpoint . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'scope' => 'read,write',
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json()['access_token'];
                return $this->accessToken;
            }

            throw new \Exception('Failed to get MonCash access token: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('MonCash authentication error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a payment
     */
    public function createPayment(float $amount, string $orderId): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '/Api/v1/CreatePayment', [
                'amount' => $amount,
                'orderId' => $orderId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'transaction_id' => $data['payment_token'] ?? null,
                    'checkout_url' => $data['mode'] === 'redirect' 
                        ? $this->endpoint . '/Moncash-middleware/Payment/Redirect?token=' . $data['payment_token']
                        : null,
                    'payment_token' => $data['payment_token'] ?? null,
                    'metadata' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'MonCash payment creation failed: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('MonCash create payment error', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'orderId' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->endpoint . '/Api/v1/RetrieveTransactionPayment', [
                'transactionId' => $transactionId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['payment']['status'] ?? 'unknown',
                    'amount' => $data['payment']['cost'] ?? 0,
                    'reference' => $data['payment']['reference'] ?? null,
                    'message' => $data['payment']['message'] ?? null,
                    'data' => $data,
                ];
            }

            return [
                'status' => 'error',
                'error' => 'Failed to retrieve transaction status',
            ];
        } catch (\Exception $e) {
            Log::error('MonCash get transaction status error', [
                'error' => $e->getMessage(),
                'transactionId' => $transactionId,
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->clientSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Process refund
     */
    public function processRefund(string $transactionId, float $amount): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '/Api/v1/Refund', [
                'transactionId' => $transactionId,
                'amount' => $amount,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Refund failed: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('MonCash refund error', [
                'error' => $e->getMessage(),
                'transactionId' => $transactionId,
                'amount' => $amount,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
