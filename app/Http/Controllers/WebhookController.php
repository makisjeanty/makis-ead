<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Mercado Pago webhook
     */
    public function mercadopago(Request $request)
    {
        Log::info('Mercado Pago Webhook received', $request->all());

        $type = $request->input('type');

        if ($type !== 'payment') {
            return response()->json(['status' => 'ignored']);
        }

        $paymentId = $request->input('data.id');

        if (!$paymentId) {
            Log::warning('Mercado Pago webhook missing data.id', $request->all());
            return response()->json(['error' => 'missing id'], 400);
        }

        try {
            $gateway = $this->paymentService->getGateway(Payment::GATEWAY_MERCADOPAGO);
            $paymentInfo = $gateway->getPaymentStatus($paymentId);

            if (!empty($paymentInfo['error'])) {
                Log::error('Mercado Pago getPaymentStatus error', ['error' => $paymentInfo['error'], 'id' => $paymentId]);
                return response()->json(['error' => 'gateway error'], 500);
            }

            // Only act on approved payments
            if (($paymentInfo['status'] ?? null) === 'approved') {
                // Try external_reference (our payment id) first
                $externalReference = $request->input('external_reference');
                $payment = null;

                if ($externalReference) {
                    $payment = Payment::find($externalReference);
                }

                // Fallback: find by transaction_id (preference id)
                if (!$payment) {
                    $payment = Payment::where('transaction_id', $paymentId)->first();
                }

                if ($payment && $payment->isPending()) {
                    $this->paymentService->confirmPayment($payment, $paymentInfo);
                    Log::info('Mercado Pago payment confirmed', ['payment_id' => $payment->id, 'gateway_id' => $paymentId]);
                } else {
                    Log::info('Mercado Pago payment not found or not pending', ['external_reference' => $externalReference, 'transaction_id' => $paymentId]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Mercado Pago Webhook Error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);
            return response()->json(['error' => 'internal error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Stripe webhook
     */
    public function stripe(Request $request)
    {
        Log::info('Stripe Webhook', $request->all());

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $gateway = $this->paymentService->getGateway(Payment::GATEWAY_STRIPE);
            
            if (!$gateway->verifyWebhook($payload, $signature)) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $event = json_decode($payload, true);

            if ($event['type'] === 'checkout.session.completed') {
                $session = $event['data']['object'];
                $paymentId = $session['metadata']['payment_id'] ?? null;
                
                if ($paymentId) {
                    $payment = Payment::find($paymentId);
                    
                    if ($payment && $payment->isPending()) {
                        $this->paymentService->confirmPayment($payment, $session);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}
