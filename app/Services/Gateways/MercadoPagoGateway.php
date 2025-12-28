<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;

class MercadoPagoGateway
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        SDK::setAccessToken($this->accessToken);
    }

    /**
     * Create a payment preference
     */
    public function createPayment(Payment $payment, array $paymentData): array
    {
        try {
            $preference = new Preference();
            
            // Create item
            $item = new Item();
            $item->title = $paymentData['title'] ?? 'Curso';
            $item->quantity = 1;
            $item->unit_price = (float) $payment->amount;
            
            $preference->items = [$item];
            
            // Set URLs
            $preference->back_urls = [
                'success' => route('checkout.success'),
                'failure' => route('checkout.failure'),
                'pending' => route('checkout.pending'),
            ];
            
            $preference->auto_return = 'approved';
            
            // Set external reference
            $preference->external_reference = $payment->id;
            
            // Set notification URL for webhooks
            $preference->notification_url = route('webhook.mercadopago');
            
            // Save preference
            $preference->save();
            
            return [
                'success' => true,
                'transaction_id' => $preference->id,
                'checkout_url' => $preference->init_point,
                'metadata' => [
                    'preference_id' => $preference->id,
                    'sandbox_init_point' => $preference->sandbox_init_point,
                ],
            ];
        } catch (\Exception $e) {
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
        // Implement webhook signature verification
        // For now, we'll return true, but you should implement proper verification
        return true;
    }

    /**
     * Get payment status from Mercado Pago
     */
    public function getPaymentStatus(string $paymentId): array
    {
        try {
            $payment = \MercadoPago\Payment::find_by_id($paymentId);
            
            return [
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'transaction_amount' => $payment->transaction_amount,
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
