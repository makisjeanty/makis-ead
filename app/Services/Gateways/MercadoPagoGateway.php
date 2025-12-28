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
        // Only call the SDK setter if the MercadoPago SDK is installed in this environment.
        if (class_exists('\\MercadoPago\\SDK')) {
            SDK::setAccessToken($this->accessToken);
        }
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
     * Verify webhook signature or fall back to best-effort validation.
     * Accepts raw payload and headers to allow cryptographic verification.
     */
    public function verifyWebhook(string $payload, array $headers = []): bool
    {
        // If a webhook secret (key) is configured, attempt HMAC verification against
        // common Mercado Pago signature headers. Supported header names: `x-meli-signature`,
        // `x-mercadopago-signature` and `x-mp-signature`.
        try {
            $webhookKey = config('services.mercadopago.webhook_key');

            if ($webhookKey) {
                $signatureHeader = null;
                $candidates = ['x-meli-signature', 'x-mercadopago-signature', 'x-mp-signature'];

                foreach ($candidates as $h) {
                    if (!empty($headers[$h])) {
                        // Header bag can be an array of values
                        $signatureHeader = is_array($headers[$h]) ? $headers[$h][0] : $headers[$h];
                        break;
                    }
                }

                if ($signatureHeader) {
                    // Compute HMAC SHA256 using the webhook key. Many providers encode
                    // the result as base64; some use hex. Compute both and compare.
                    $hmacRaw = hash_hmac('sha256', $payload, $webhookKey, true);
                    $hmacBase64 = base64_encode($hmacRaw);
                    $hmacHex = bin2hex($hmacRaw);

                    if (hash_equals($hmacBase64, $signatureHeader) || hash_equals($hmacHex, $signatureHeader)) {
                        return true;
                    }
                    // If a signature header exists but doesn't match, reject immediately.
                    return false;
                }
            }

            // Fallback: if no webhook key or signature header is present, perform
            // best-effort validation by extracting an id from the payload and
            // checking the payment status via the Mercado Pago API.
            $data = json_decode($payload, true);

            $paymentId = null;
            if (!empty($data['data']['id'])) {
                $paymentId = $data['data']['id'];
            } elseif (!empty($data['id'])) {
                $paymentId = $data['id'];
            }

            if (!$paymentId) {
                return false;
            }

            $info = $this->getPaymentStatus($paymentId);

            return empty($info['error']);
        } catch (\Exception $e) {
            return false;
        }
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
