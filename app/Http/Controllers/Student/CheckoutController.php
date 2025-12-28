<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class CheckoutController extends Controller
{
    public function purchase($slug)
    {
        $user = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        // 1. Já tem o curso?
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.classroom.watch', $course->slug)
                ->with('info', 'Você já possui este curso!');
        }

        // 2. Tenta pagar com a Wallet
        try {
            // Tenta debitar (lança erro se não tiver saldo)
            $user->wallet->withdraw(
                $course->price, 
                "Compra: {$course->title}", 
                'CRS-' . $course->id
            );

            // 3. Se passou, cria Matrícula e Registro de Pagamento
            Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'transaction_id' => 'WALLET-' . Str::random(10),
                'provider' => 'wallet_balance', // Importante para saber a origem
                'amount' => $course->price,
                'status' => 'paid',
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress' => 0,
            ]);

            return redirect()->route('student.classroom.watch', $course->slug)
                ->with('success', 'Curso comprado com seu saldo da Carteira!');

        } catch (Exception $e) {
            // Se falhar (saldo insuficiente), redireciona para recarga
            return redirect()->route('student.wallet.index')
                ->with('error', 'Saldo insuficiente para este curso (HTG ' . $course->price . '). Por favor, recarregue.');
        }
    }
}
