<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Display the specified cart.
     */
    public function show(string $id)
    {
        $cart = Cart::with('items.product')->findOrFail($id);

        return response()->json($cart, 200);
    }
}
