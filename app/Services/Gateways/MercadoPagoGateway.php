<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use Illuminate\Support\Facades\Log;

class MercadoPagoGateway
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        if ($this->accessToken) {
            MercadoPagoConfig::setAccessToken($this->accessToken);
        }
    }

    /**
     * Create a payment preference
     */
    public function createPayment(Payment $payment, array $paymentData): array
    {
        try {
            $client = new PreferenceClient();
            
            $preferenceRequest = [
                "items" => [
                    [
                        "title" => $paymentData['title'] ?? 'Curso',
                        "quantity" => 1,
                        "unit_price" => (float) $payment->amount,
                        "currency_id" => "BRL" // Adjust if using other currency
                    ]
                ],
                "back_urls" => [
                    "success" => route('checkout.success'),
                    "failure" => route('checkout.failure'),
                    "pending" => route('checkout.pending')
                ],
                "auto_return" => "approved",
                "external_reference" => (string) $payment->id,
                "notification_url" => route('webhook.mercadopago'),
                "metadata" => [
                    "payment_id" => $payment->id,
                    "user_id" => $payment->user_id
                ]
            ];

            $preference = $client->create($preferenceRequest);
            
            return [
                'success' => true,
                'transaction_id' => $preference->id,
                'checkout_url' => $preference->init_point, // Use init_point for production
                'metadata' => [
                    'preference_id' => $preference->id,
                    'sandbox_init_point' => $preference->sandbox_init_point,
                ],
            ];
        } catch (\Exception $e) {
            Log::error("MercadoPago Create Payment Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(array $data): bool
    {
        // Implement webhook signature verification if needed
        return true;
    }

    /**
     * Get payment status from Mercado Pago
     */
    public function getPaymentStatus(string $paymentId): array
    {
        try {
            $client = new PaymentClient();
            $payment = $client->get($paymentId);
            
            return [
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'transaction_amount' => $payment->transaction_amount,
            ];
        } catch (\Exception $e) {
            Log::error("MercadoPago Get Status Error: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
