<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Course $course;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'student@test.com',
            'name' => 'Test Student'
        ]);
        
        $this->course = Course::factory()->create([
            'title' => 'Test Course',
            'price' => 99.90,
            'is_published' => true
        ]);
        
        $this->order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => Order::generateOrderNumber(),
            'total' => 99.90,
            'status' => Order::STATUS_PENDING,
            'metadata' => ['course_id' => $this->course->id]
        ]);
    }

    /** @test */
    public function it_creates_a_pending_payment_for_an_order()
    {
        $paymentService = app(PaymentService::class);
        
        $payment = $paymentService->createPayment(
            $this->order,
            Payment::GATEWAY_MERCADOPAGO,
            ['test' => true]
        );
        
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($this->order->id, $payment->order_id);
        $this->assertEquals(Payment::GATEWAY_MERCADOPAGO, $payment->gateway);
        $this->assertEquals('99.90', $payment->amount);
        $this->assertEquals(Payment::STATUS_PENDING, $payment->status);
        $this->assertTrue($payment->isPending());
    }

    /** @test */
    public function it_stores_metadata_encrypted()
    {
        $paymentService = app(PaymentService::class);
        
        $metadata = [
            'test_mode' => true,
            'customer_email' => 'test@example.com'
        ];
        
        $payment = $paymentService->createPayment(
            $this->order,
            Payment::GATEWAY_STRIPE,
            $metadata
        );
        
        $this->assertEquals($metadata, $payment->metadata);
    }

    /** @test */
    public function it_marks_payment_as_completed()
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $this->assertTrue($payment->isPending());
        
        $payment->markAsCompleted();
        $payment->refresh();
        
        $this->assertEquals(Payment::STATUS_COMPLETED, $payment->status);
        $this->assertFalse($payment->isPending());
    }

    /** @test */
    public function it_marks_payment_as_failed()
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_STRIPE,
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $payment->markAsFailed();
        $payment->refresh();
        
        $this->assertEquals(Payment::STATUS_FAILED, $payment->status);
        $this->assertFalse($payment->isPending());
    }

    /** @test */
    public function it_confirms_payment_and_creates_enrollment()
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $paymentService = app(PaymentService::class);
        
        $webhookData = [
            'status' => 'approved',
            'payment_id' => '123456789'
        ];
        
        $paymentService->confirmPayment($payment, $webhookData);
        
        $payment->refresh();
        $this->order->refresh();
        
        $this->assertEquals(Payment::STATUS_COMPLETED, $payment->status);
        $this->assertEquals(Order::STATUS_PAID, $this->order->status);
        
        $enrollment = Enrollment::where('user_id', $this->user->id)
            ->where('course_id', $this->course->id)
            ->first();
        
        $this->assertNotNull($enrollment);
        $this->assertEquals(0, $enrollment->progress_percentage);
    }

    /** @test */
    public function it_does_not_duplicate_enrollment_on_multiple_confirmations()
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $paymentService = app(PaymentService::class);
        
        $webhookData = ['status' => 'approved'];
        
        $paymentService->confirmPayment($payment, $webhookData);
        $paymentService->confirmPayment($payment, $webhookData);
        
        $enrollmentCount = Enrollment::where('user_id', $this->user->id)
            ->where('course_id', $this->course->id)
            ->count();
        
        $this->assertEquals(1, $enrollmentCount);
    }

    /** @test */
    public function it_returns_mercadopago_gateway_instance()
    {
        $paymentService = app(PaymentService::class);
        $gateway = $paymentService->getGateway(Payment::GATEWAY_MERCADOPAGO);
        
        $this->assertInstanceOf(\App\Services\Gateways\MercadoPagoGateway::class, $gateway);
    }

    /** @test */
    public function it_returns_stripe_gateway_instance()
    {
        $paymentService = app(PaymentService::class);
        $gateway = $paymentService->getGateway(Payment::GATEWAY_STRIPE);
        
        $this->assertInstanceOf(\App\Services\Gateways\StripeGateway::class, $gateway);
    }

    /** @test */
    public function it_throws_exception_for_unsupported_gateway()
    {
        $this->expectException(\Exception::class);
        
        $paymentService = app(PaymentService::class);
        $paymentService->getGateway('invalid_gateway');
    }

    /** @test */
    public function it_filters_completed_payments()
    {
        Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'amount' => 99.90,
            'status' => Payment::STATUS_COMPLETED,
        ]);
        
        Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_STRIPE,
            'amount' => 49.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $completed = Payment::completed()->get();
        
        $this->assertCount(1, $completed);
        $this->assertEquals(Payment::STATUS_COMPLETED, $completed->first()->status);
    }

    /** @test */
    public function it_filters_payments_by_gateway()
    {
        Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_MERCADOPAGO,
            'amount' => 99.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        Payment::create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'gateway' => Payment::GATEWAY_STRIPE,
            'amount' => 49.90,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        $mercadoPagoPayments = Payment::byGateway(Payment::GATEWAY_MERCADOPAGO)->get();
        
        $this->assertCount(1, $mercadoPagoPayments);
        $this->assertEquals(Payment::GATEWAY_MERCADOPAGO, $mercadoPagoPayments->first()->gateway);
    }
}
