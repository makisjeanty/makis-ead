<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WalletWebhookController;
use App\Http\Controllers\Student\ClassroomController;
use App\Http\Controllers\Student\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página inicial
Route::get('/', function () {
    if (Schema::hasTable('courses')) {
        $courses = \App\Models\Course::where('is_published', true)
            ->orderBy('students_count', 'desc')
            ->limit(6)
            ->get();

        $categories = \App\Models\Course::where('is_published', true)
            ->select('category', \DB::raw('count(*) as courses_count'))
            ->groupBy('category')
            ->get();
    } else {
        $courses = collect();
        $categories = collect();
    }

    return view('welcome', compact('courses', 'categories'));
});

// Cursos
Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('/cursos/{slug}', [CourseController::class, 'show'])->name('courses.show');

// Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Carrinho
Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/adicionar/{course}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/carrinho/remover/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrinho/limpar', [CartController::class, 'clear'])->name('cart.clear');

// Checkout (somente para usuários autenticados)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::get('/checkout/pending', [CheckoutController::class, 'pending'])->name('checkout.pending');
});

// Webhooks (fora de auth)
Route::post('/webhook/mercadopago', [WebhookController::class, 'mercadopago'])->name('webhook.mercadopago');
Route::post('/webhook/stripe', [WebhookController::class, 'stripe'])->name('webhook.stripe');
Route::post('/webhook/stripe/subscription', [StripeWebhookController::class, 'handle'])->name('webhook.stripe.subscription');
Route::post('/webhook/moncash/wallet', [WalletWebhookController::class, 'moncash'])->name('webhook.moncash.wallet');

// Wallet (usuário autenticado)
Route::middleware(['auth', 'verified'])->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::get('/deposit', [WalletController::class, 'deposit'])->name('deposit');
    Route::post('/deposit', [WalletController::class, 'processDeposit'])->name('deposit.process');
    Route::get('/deposit/success', [WalletController::class, 'depositSuccess'])->name('deposit.success');
    Route::get('/deposit/failure', [WalletController::class, 'depositFailure'])->name('deposit.failure');
    Route::get('/history', [WalletController::class, 'history'])->name('history');
});

// Currency Switcher
Route::post('/currency/set', [CurrencyController::class, 'setCurrency'])->name('currency.set');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Subscription
Route::get('/pricing', function () {
    // Simple pricing page without subscription plans for now
    $plans = [];
    return view('pricing.index', compact('plans'));
})->name('pricing');
Route::middleware(['auth', 'verified'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
    Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
    Route::get('/portal', [SubscriptionController::class, 'portal'])->name('portal');
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('cancel-subscription');
    Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
});

// --- ÁREA DO ALUNO ---
Route::middleware(['auth', 'verified'])->prefix('aluno')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/meus-cursos', [ClassroomController::class, 'index'])->name('courses.index');
    Route::get('/curso/{slug}/aula/{lesson?}', [ClassroomController::class, 'watch'])->name('classroom.watch');
});

// Redirecionamentos padrão
Route::get('/dashboard', function () {
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/perfil', function () {
    return redirect()->route('student.courses.index');
})->middleware(['auth', 'verified']);

// Perfil (compatibilidade com testes padrão em inglês)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
});

// Autenticação (Breeze)
require __DIR__ . '/auth.php';
