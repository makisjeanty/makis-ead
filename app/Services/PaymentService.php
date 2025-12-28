<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Gateways\MercadoPagoGateway;
use App\Services\Gateways\StripeGateway;

class PaymentService
{
    /**
     * Create a payment for an order
     */
    public function createPayment(Order $order, string $gateway, array $metadata = []): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'gateway' => $gateway,
            'amount' => $order->total,
            'status' => Payment::STATUS_PENDING,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get the appropriate gateway instance
     */
    public function getGateway(string $gateway)
    {
        return match($gateway) {
            Payment::GATEWAY_MERCADOPAGO => new MercadoPagoGateway(),
            Payment::GATEWAY_STRIPE => new StripeGateway(),
            default => throw new \Exception("Gateway não suportado: {$gateway}"),
        };
    }

    /**
     * Process payment through the specified gateway
     */
    public function processPayment(Payment $payment, array $paymentData)
    {
        $gateway = $this->getGateway($payment->gateway);
        
        try {
            $result = $gateway->createPayment($payment, $paymentData);
            
            if ($result['success']) {
                $payment->update([
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'metadata' => array_merge($payment->metadata ?? [], $result['metadata'] ?? []),
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            $payment->markAsFailed();
            throw $e;
        }
    }

    /**
     * Handle payment confirmation (from webhook)
     */
    public function confirmPayment(Payment $payment, array $webhookData): void
    {
        $payment->markAsCompleted();
        $payment->order->markAsPaid();
        
        // Create enrollment for the user
        $this->createEnrollmentFromOrder($payment->order);
    }

    /**
     * Create enrollment after successful payment
     */
    protected function createEnrollmentFromOrder(Order $order): void
    {
        // Assuming order metadata contains course_id
        $courseId = $order->metadata['course_id'] ?? null;
        
        if ($courseId) {
            \App\Models\Enrollment::firstOrCreate([
                'user_id' => $order->user_id,
                'course_id' => $courseId,
            ], [
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);
        }
    }
}
