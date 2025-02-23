<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
class ReportController extends Controller
{
    public function orderReport()
    {
        $orders = Order::all(); // Fetch all orders
        return response()->json($orders);
    }

    public function paymentReport()
    {
        $payments = Payment::all(); // Fetch all payments
        return response()->json($payments);
    }
}
