<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use App\Services\PaymentService;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
    }

    /**
     * Show checkout page
     */
    public function index(Request $request)
    {
        // Use persisted cart items for authenticated user
        $cartItems = CartItem::with('course')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('courses.index')->with('error', 'Seu carrinho está vazio');
        }

        $courseIds = $cartItems->pluck('course_id')->toArray();
        $courses = Course::whereIn('id', $courseIds)->get();
        $total = $cartItems->sum('price');

        return view('checkout.index', compact('courses', 'total'));
    }

    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'gateway' => 'required|in:mercadopago,stripe',
        ]);

        // Read persisted cart items for authenticated user
        $cartItems = CartItem::with('course')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('courses.index')->with('error', 'Seu carrinho está vazio');
        }

        $courseIds = $cartItems->pluck('course_id')->toArray();
        $courses = Course::whereIn('id', $courseIds)->get();
        $total = $cartItems->sum('price');

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'total' => $total,
            'status' => Order::STATUS_PENDING,
            'payment_method' => $request->gateway,
            'metadata' => [
                'course_id' => $courses->first()->id, // For single course purchase
                'courses' => $courses->pluck('id')->toArray(),
            ],
        ]);

        // Create payment
        $payment = $this->paymentService->createPayment($order, $request->gateway);

        // Process payment through gateway
        $result = $this->paymentService->processPayment($payment, [
            'title' => $courses->count() > 1 
                ? "Compra de {$courses->count()} cursos" 
                : $courses->first()->title,
        ]);

        if ($result['success']) {
            // Clear cart
            session()->forget('cart');
            
            // Redirect to payment gateway
            return redirect($result['checkout_url']);
        }

        return redirect()->back()->with('error', 'Erro ao processar pagamento: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Payment success callback
     */
    public function success(Request $request)
    {
        return view('checkout.success');
    }

    /**
     * Payment failure callback
     */
    public function failure(Request $request)
    {
        return view('checkout.failure');
    }

    /**
     * Payment pending callback
     */
    public function pending(Request $request)
    {
        return view('checkout.pending');
    }
}
