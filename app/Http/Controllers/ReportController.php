<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\Tag(
 *     name="Reports",
 *     description="API Endpoints for generating various reports"
 * )
 */
class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reports/orders",
     *     summary="Get all orders report",
     *     description="Retrieves a comprehensive report of all orders in the system",
     *     operationId="orderReport",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function orderReport()
    {
        $orders = Order::all(); // Fetch all orders
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/payments",
     *     summary="Get all payments report",
     *     description="Retrieves a comprehensive report of all payments in the system",
     *     operationId="paymentReport",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="amount", type="number", format="float", example=299.99),
     *                 @OA\Property(property="payment_method", type="string", example="credit_card"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function paymentReport()
    {
        $payments = Payment::all(); // Fetch all payments
        return response()->json($payments);
    }
}
