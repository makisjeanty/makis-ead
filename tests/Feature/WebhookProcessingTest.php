<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Course $course;
    protected Order $order;
    protected Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->course = Course::factory()->create(['price' => 99.90]);
        
        $this->order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => Order::generateOrderNumber(),
            'total' => 99.90,
            'status' => Order::STATUS_PENDING,
            'metadata' => ['course_id' => $this->course->id]
        ]);
        
        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'transaction_id' => 'test_123456',
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function webhook_event_table_stores_events()
    {
        $event = WebhookEvent::create([
            'event_id' => 'test_event_123',
            'gateway' => 'mercadopago',
            'payload' => ['test' => 'data'],
            'status' => 'success',
            'result' => ['payment_id' => 1],
            'processed_at' => now(),
        ]);

        $this->assertDatabaseHas('webhook_events', [
            'event_id' => 'test_event_123',
            'gateway' => 'mercadopago',
            'status' => 'success',
        ]);
        
        $this->assertEquals(['test' => 'data'], $event->payload);
        $this->assertEquals(['payment_id' => 1], $event->result);
    }

    /** @test */
    public function webhook_event_scopes_work_correctly()
    {
        WebhookEvent::create([
            'event_id' => 'mp_event_1',
            'gateway' => 'mercadopago',
            'payload' => [],
            'status' => 'success',
            'processed_at' => now(),
        ]);

        WebhookEvent::create([
            'event_id' => 'stripe_event_1',
            'gateway' => 'stripe',
            'payload' => [],
            'status' => 'failed',
            'processed_at' => now(),
        ]);

        $mercadoPagoEvents = WebhookEvent::byGateway('mercadopago')->get();
        $this->assertCount(1, $mercadoPagoEvents);

        $successfulEvents = WebhookEvent::successful()->get();
        $this->assertCount(1, $successfulEvents);

        $failedEvents = WebhookEvent::failed()->get();
        $this->assertCount(1, $failedEvents);
    }

    /** @test */
    public function payment_model_has_pending_check()
    {
        $this->assertTrue($this->payment->isPending());
        
        $this->payment->update(['status' => Payment::STATUS_COMPLETED]);
        $this->assertFalse($this->payment->isPending());
    }

    /** @test */
    public function webhook_events_enforce_unique_event_id()
    {
        WebhookEvent::create([
            'event_id' => 'unique_event_123',
            'gateway' => 'mercadopago',
            'payload' => [],
            'status' => 'success',
            'processed_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        WebhookEvent::create([
            'event_id' => 'unique_event_123',
            'gateway' => 'stripe',
            'payload' => [],
            'status' => 'success',
            'processed_at' => now(),
        ]);
    }
}
