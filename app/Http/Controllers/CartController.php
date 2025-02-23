<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
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
