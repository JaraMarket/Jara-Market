<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

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
