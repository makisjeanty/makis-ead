<?php

namespace App\Services\Gateways;

use App\Models\Payment;

class StripeGateway
{
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        
        if ($this->secretKey) {
            \Stripe\Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Create a payment intent for one-time course purchase
     */
    public function createPayment(Payment $payment, array $paymentData): array
    {
        if (!$this->secretKey) {
            return [
                'success' => false,
                'error' => 'Stripe não configurado',
            ];
        }

        try {
            // Get course price or use default R$ 39.90
            $coursePrice = $paymentData['price'] ?? 39.90;
            $courseName = $paymentData['title'] ?? 'Curso';

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => $courseName,
                            'description' => $paymentData['description'] ?? 'Acesso vitalício ao curso',
                        ],
                        'unit_amount' => (int) ($coursePrice * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment', // One-time payment, not subscription
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.failure'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'course_id' => $paymentData['course_id'] ?? null,
                ],
            ]);

            return [
                'success' => true,
                'transaction_id' => $session->id,
                'checkout_url' => $session->url,
                'metadata' => [
                    'session_id' => $session->id,
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
    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create a checkout session for subscription
     */
    public function createSubscriptionCheckout(
        int $userId,
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        array $metadata = []
    ): array {
        try {
            $sessionData = [
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'client_reference_id' => (string) $userId,
                'metadata' => array_merge([
                    'user_id' => $userId,
                ], $metadata),
            ];

            // Add trial period if configured
            $trialDays = config('stripe.trial_days', 0);
            if ($trialDays > 0) {
                $sessionData['subscription_data'] = [
                    'trial_period_days' => $trialDays,
                ];
            }

            $session = \Stripe\Checkout\Session::create($sessionData);

            return [
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe subscription checkout failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a customer portal session
     */
    public function createPortalSession(string $customerId, string $returnUrl): array
    {
        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $customerId,
                'return_url' => $returnUrl,
            ]);

            return [
                'success' => true,
                'url' => $session->url,
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe portal session failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get subscription details
     */
    public function getSubscription(string $subscriptionId): ?\Stripe\Subscription
    {
        try {
            return \Stripe\Subscription::retrieve($subscriptionId);
        } catch (\Exception $e) {
            \Log::error('Stripe subscription retrieval failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId, bool $atPeriodEnd = true): array
    {
        try {
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            if ($atPeriodEnd) {
                $subscription->update([
                    'cancel_at_period_end' => true,
                ]);
            } else {
                $subscription->cancel();
            }

            return [
                'success' => true,
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe subscription cancellation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Resume a canceled subscription
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        try {
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
            $subscription->update([
                'cancel_at_period_end' => false,
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe subscription resume failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update subscription (change plan)
     */
    public function updateSubscriptionPlan(string $subscriptionId, string $newPriceId): array
    {
        try {
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            \Stripe\Subscription::update($subscriptionId, [
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'price' => $newPriceId,
                    ],
                ],
                'proration_behavior' => 'always_invoice',
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe subscription update failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get plan configuration by price ID
     */
    public function getPlanByPriceId(string $priceId): ?array
    {
        $plans = config('stripe.plans');

        foreach ($plans as $key => $plan) {
            if ($plan['price_id'] === $priceId) {
                return array_merge($plan, ['key' => $key]);
            }
        }

        return null;
    }
}

