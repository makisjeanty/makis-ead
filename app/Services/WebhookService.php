<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookService
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process MercadoPago webhook with idempotency and retry handling
     */
    public function processMercadoPago(array $webhookData): array
    {
        $type = $webhookData['type'] ?? null;
        $paymentId = $webhookData['data']['id'] ?? null;
        
        if ($type !== 'payment') {
            return ['status' => 'ignored', 'reason' => 'not_payment_event'];
        }

        if (!$paymentId) {
            Log::warning('MercadoPago webhook missing data.id', $webhookData);
            return ['status' => 'error', 'reason' => 'missing_payment_id'];
        }

        $eventId = $this->generateEventId('mercadopago', $webhookData);
        
        if ($this->isDuplicateEvent($eventId)) {
            Log::info('Duplicate MercadoPago webhook ignored', ['event_id' => $eventId]);
            return ['status' => 'duplicate', 'event_id' => $eventId];
        }

        try {
            $result = $this->processWithRetry(function() use ($webhookData, $paymentId) {
                return $this->handleMercadoPagoPayment($webhookData, $paymentId);
            });

            $this->recordWebhookEvent($eventId, 'mercadopago', $webhookData, 'success', $result);
            
            return array_merge($result, ['event_id' => $eventId]);
            
        } catch (\Exception $e) {
            Log::error('MercadoPago webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $webhookData,
            ]);
            
            $this->recordWebhookEvent($eventId, 'mercadopago', $webhookData, 'failed', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Process Stripe webhook with idempotency and retry handling
     */
    public function processStripe(string $payload, ?string $signature): array
    {
        $gateway = $this->paymentService->getGateway(Payment::GATEWAY_STRIPE);
        
        if (!$gateway->verifyWebhook($payload, $signature)) {
            Log::warning('Invalid Stripe webhook signature');
            return ['status' => 'error', 'reason' => 'invalid_signature'];
        }

        $event = json_decode($payload, true);
        $eventId = $this->generateEventId('stripe', $event);
        
        if ($this->isDuplicateEvent($eventId)) {
            Log::info('Duplicate Stripe webhook ignored', ['event_id' => $eventId]);
            return ['status' => 'duplicate', 'event_id' => $eventId];
        }

        try {
            $result = $this->processWithRetry(function() use ($event) {
                return $this->handleStripeEvent($event);
            });

            $this->recordWebhookEvent($eventId, 'stripe', $event, 'success', $result);
            
            return array_merge($result, ['event_id' => $eventId]);
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event' => $event,
            ]);
            
            $this->recordWebhookEvent($eventId, 'stripe', $event, 'failed', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle MercadoPago payment processing
     */
    protected function handleMercadoPagoPayment(array $webhookData, string $gatewayPaymentId): array
    {
        $gateway = $this->paymentService->getGateway(Payment::GATEWAY_MERCADOPAGO);
        $paymentInfo = $gateway->getPaymentStatus($gatewayPaymentId);

        if (!empty($paymentInfo['error'])) {
            throw new \Exception("MercadoPago API error: " . $paymentInfo['error']);
        }

        if (($paymentInfo['status'] ?? null) !== 'approved') {
            return [
                'status' => 'ignored',
                'reason' => 'payment_not_approved',
                'payment_status' => $paymentInfo['status'] ?? 'unknown'
            ];
        }

        $payment = $this->findPaymentForMercadoPago($webhookData, $gatewayPaymentId);

        if (!$payment) {
            return [
                'status' => 'ignored',
                'reason' => 'payment_not_found',
                'gateway_id' => $gatewayPaymentId
            ];
        }

        if (!$payment->isPending()) {
            return [
                'status' => 'already_processed',
                'payment_id' => $payment->id,
                'current_status' => $payment->status
            ];
        }

        return DB::transaction(function() use ($payment, $paymentInfo) {
            $this->paymentService->confirmPayment($payment, $paymentInfo);
            
            Log::info('MercadoPago payment confirmed', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'amount' => $payment->amount
            ]);

            return [
                'status' => 'success',
                'payment_id' => $payment->id,
                'action' => 'payment_confirmed'
            ];
        });
    }

    /**
     * Handle Stripe event processing
     */
    protected function handleStripeEvent(array $event): array
    {
        if ($event['type'] !== 'checkout.session.completed') {
            return ['status' => 'ignored', 'reason' => 'unsupported_event_type'];
        }

        $session = $event['data']['object'];
        $paymentId = $session['metadata']['payment_id'] ?? null;

        if (!$paymentId) {
            return ['status' => 'ignored', 'reason' => 'missing_payment_id_in_metadata'];
        }

        $payment = Payment::find($paymentId);

        if (!$payment) {
            return ['status' => 'ignored', 'reason' => 'payment_not_found', 'payment_id' => $paymentId];
        }

        if (!$payment->isPending()) {
            return [
                'status' => 'already_processed',
                'payment_id' => $payment->id,
                'current_status' => $payment->status
            ];
        }

        return DB::transaction(function() use ($payment, $session) {
            $this->paymentService->confirmPayment($payment, $session);
            
            Log::info('Stripe payment confirmed', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'amount' => $payment->amount
            ]);

            return [
                'status' => 'success',
                'payment_id' => $payment->id,
                'action' => 'payment_confirmed'
            ];
        });
    }

    /**
     * Find payment for MercadoPago webhook
     */
    protected function findPaymentForMercadoPago(array $webhookData, string $gatewayPaymentId): ?Payment
    {
        $externalReference = $webhookData['external_reference'] ?? null;
        
        if ($externalReference) {
            $payment = Payment::find($externalReference);
            if ($payment) {
                return $payment;
            }
        }

        return Payment::where('transaction_id', $gatewayPaymentId)
            ->where('gateway', Payment::GATEWAY_MERCADOPAGO)
            ->first();
    }

    /**
     * Generate unique event ID for idempotency
     */
    protected function generateEventId(string $gateway, array $data): string
    {
        if ($gateway === 'stripe' && isset($data['id'])) {
            return "stripe_{$data['id']}";
        }
        
        if ($gateway === 'mercadopago') {
            $paymentId = $data['data']['id'] ?? '';
            $action = $data['action'] ?? '';
            $dateCreated = $data['date_created'] ?? '';
            return "mercadopago_{$paymentId}_{$action}_{$dateCreated}";
        }
        
        return $gateway . '_' . md5(json_encode($data));
    }

    /**
     * Check if webhook event was already processed (idempotency)
     */
    protected function isDuplicateEvent(string $eventId): bool
    {
        $cacheKey = "webhook_event:{$eventId}";
        
        if (Cache::has($cacheKey)) {
            return true;
        }

        if (WebhookEvent::where('event_id', $eventId)->exists()) {
            Cache::put($cacheKey, true, now()->addHours(24));
            return true;
        }

        Cache::put($cacheKey, true, now()->addHours(24));
        return false;
    }

    /**
     * Record webhook event for audit trail
     */
    protected function recordWebhookEvent(
        string $eventId,
        string $gateway,
        array $payload,
        string $status,
        array $result = []
    ): void {
        WebhookEvent::create([
            'event_id' => $eventId,
            'gateway' => $gateway,
            'payload' => $payload,
            'status' => $status,
            'result' => $result,
            'processed_at' => now(),
        ]);
    }

    /**
     * Execute operation with retry mechanism
     */
    protected function processWithRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $lastException = null;
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;
                
                if ($attempt < $maxAttempts) {
                    $waitTime = $this->getExponentialBackoff($attempt);
                    Log::warning("Webhook processing attempt {$attempt} failed, retrying in {$waitTime}ms", [
                        'error' => $e->getMessage()
                    ]);
                    usleep($waitTime * 1000);
                }
            }
        }
        
        throw $lastException;
    }

    /**
     * Calculate exponential backoff delay
     */
    protected function getExponentialBackoff(int $attempt): int
    {
        return min(1000 * (2 ** ($attempt - 1)), 10000);
    }
}
