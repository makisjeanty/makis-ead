<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Gateways\MercadoPagoGateway;

class MercadoPagoWebhookTest extends TestCase
{
    public function test_verify_webhook_with_valid_signature_returns_true()
    {
        config(['services.mercadopago.webhook_key' => 'test_webhook_key']);

        $payload = json_encode(['type' => 'payment', 'data' => ['id' => 'tx_12345']]);

        // compute expected signature (base64 of HMAC-SHA256)
        $hmac = hash_hmac('sha256', $payload, 'test_webhook_key', true);
        $sig = base64_encode($hmac);

        $gateway = new MercadoPagoGateway();

        $this->assertTrue($gateway->verifyWebhook($payload, ['x-meli-signature' => [$sig]]));
    }

    public function test_webhook_controller_accepts_verified_payload()
    {
        // Bind a fake PaymentService that returns a stub gateway
        $this->app->bind(\App\Services\PaymentService::class, function () {
            return new class extends \App\Services\PaymentService {
                public function getGateway($name)
                {
                    return new class {
                        public function verifyWebhook($payload, $headers = []) { return true; }
                        public function getPaymentStatus($id) { return ['status' => 'approved']; }
                    };
                }

                public function confirmPayment(\App\Models\Payment $payment, array $webhookData): void { /* noop for test */ }
            };
        });

        $payload = json_encode(['type' => 'payment', 'data' => ['id' => 'tx_12345']]);

        $response = $this->postJson(route('webhook.mercadopago'), json_decode($payload, true));

        $response->assertStatus(200)->assertJson(['status' => 'ok']);
    }
}
