<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "total"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="total", type="number", format="float", example=99.99),
     *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created successfully"),
     *     @OA\Response(response=400, description="Bad request")
     * )
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
            'shipping_fee' => 'nullable|numeric',
            'status' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => $validated['user_id'],
            'total' => $validated['total'],
            'shipping_fee' => $validated['shipping_fee'] ?? 0,
            'status' => $validated['status'] ?? 'pending',
        ]);

        // Attach items to the order
        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? 0, // Assuming price is passed or calculated
            ]);
        }

        return response()->json($order, 201);
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(string $id)
    {
        $order = Order::findOrFail($id);

        // Check if the order can be canceled
        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Only pending orders can be canceled.'], 400);
        }

        // Update the order status to canceled
        $order->status = 'canceled';
        $order->save();

        return response()->json(['message' => 'Order canceled successfully.'], 200);
    }

    /**
     * Display the specified order summary.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        return response()->json($order, 200);
    }

    /**
     * Display the specified order receipt.
     */
    public function receipt(string $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        // Here you can format the order details as a receipt
        $receipt = [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'total' => $order->total,
            'shipping_fee' => $order->shipping_fee,
            'status' => $order->status,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
            'created_at' => $order->created_at,
        ];

        return response()->json($receipt, 200);
    }

    /**
     * Display the tracking information for the specified order.
     */
    public function track(string $id)
    {
        $order = Order::findOrFail($id);

        // Here you can format the order details as tracking information
        $trackingInfo = [
            'order_id' => $order->id,
            'status' => $order->status,
            'estimated_delivery' => $order->estimated_delivery ?? 'Not available',
            'tracking_updates' => $order->tracking_updates ?? [], // Assuming tracking updates are stored
        ];

        return response()->json($trackingInfo, 200);
    }

    /**
     * Display a listing of the orders for a specific user.
     */
    public function userOrders(string $userId)
    {
        $orders = Order::where('user_id', $userId)->with('items.product')->get();

        return response()->json($orders, 200);
    }

    public function index()
    {
        return Order::all();
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order updated successfully']);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
