<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CartItem;
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
        CartItem::create([
            "user_id" => auth()->id(),
            "session_id" => session()->getId(),
            "course_id" => $course->id,
            "price" => $course->price,
        ]);

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
