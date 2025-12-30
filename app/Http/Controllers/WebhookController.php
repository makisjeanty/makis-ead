<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle Mercado Pago webhook
     */
    public function mercadopago(Request $request)
    {
        Log::info('MercadoPago webhook received', $request->all());

        try {
            $result = $this->webhookService->processMercadoPago($request->all());
            
            $statusCode = match($result['status']) {
                'error' => 400,
                'duplicate', 'ignored', 'already_processed' => 200,
                'success' => 200,
                default => 200
            };
            
            return response()->json($result, $statusCode);
            
        } catch (\Exception $e) {
            Log::error('MercadoPago webhook processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function stripe(Request $request)
    {
        Log::info('Stripe webhook received');

        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');
            
            $result = $this->webhookService->processStripe($payload, $signature);
            
            $statusCode = match($result['status']) {
                'error' => 400,
                'duplicate', 'ignored', 'already_processed' => 200,
                'success' => 200,
                default => 200
            };
            
            return response()->json($result, $statusCode);
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
