<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CartItem;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->check() 
            ? CartItem::with("course")->where("user_id", auth()->id())->get()
            : CartItem::with("course")->where("session_id", session()->getId())->get();

        $total = $cartItems->sum("price");

        return view("cart.index", compact("cartItems", "total"));
    }

    public function add(Course $course)
    {
        // 1. FREE COURSE LOGIC (Direct Enrollment)
        if ($course->price == 0) {
            if (!auth()->check()) {
                // Store intended URL to redirect back after login
                // We redirect back to this method to process the enrollment
                session()->put('url.intended', route('cart.add', $course));
                return redirect()->route('login');
            }

            $user = auth()->user();

            // Check if already enrolled
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['enrolled_at' => now(), 'progress_percentage' => 0]
            );

            if ($enrollment->wasRecentlyCreated) {
                $course->increment('students_count');
            }

            return redirect()->route('student.classroom.watch', $course->slug)
                ->with('success', 'Você foi matriculado gratuitamente!');
        }

        // 2. PAID COURSE LOGIC (Add to Cart)
        
        // Prevent duplicates in cart
        $exists = CartItem::where('course_id', $course->id)
            ->where(function($query) {
                if (auth()->check()) {
                    $query->where('user_id', auth()->id());
                } else {
                    $query->where('session_id', session()->getId());
                }
            })->exists();

        if (!$exists) {
            CartItem::create([
                "user_id" => auth()->id(),
                "session_id" => session()->getId(),
                "course_id" => $course->id,
                "price" => $course->price,
            ]);
        }

        return redirect()->route("cart.index")->with("success", "Curso adicionado!");
    }

    public function remove(CartItem $item)
    {
        $item->delete();
        return redirect()->route("cart.index")->with("success", "Item removido!");
    }

    public function clear()
    {
        if (auth()->check()) {
            CartItem::where("user_id", auth()->id())->delete();
        } else {
            CartItem::where("session_id", session()->getId())->delete();
        }
        return redirect()->route("cart.index")->with("success", "Carrinho limpo!");
    }
}
