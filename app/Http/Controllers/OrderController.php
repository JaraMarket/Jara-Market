<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\OrderService;
use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService)
    { }


    public function index()
    {
        return view('orders.index');
    }

    public function getData(Request $request)
    {
        $query = Order::with('user')
            ->when($request->status, function ($q) use ($request) {
                // If status is selected → filter by status
                $q->where('status', $request->status);
            }, function ($q) {
                // If status is empty → return today's orders
                $q->whereDate('created_at', now()->toDateString());
            });

        return DataTables::of($query)
            ->addColumn('customer', fn($order) => $order->user->name ?? 'N/A')
            ->editColumn('total', fn($order) => '₦' . number_format($order->total, 2))
            ->editColumn('status', function ($order) {
                $classes = [
                    'pending'    => 'bg-yellow-100 text-yellow-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'completed'  => 'bg-green-100 text-green-800',
                    'cancelled'  => 'bg-red-100 text-red-800',
                ];
                $class = $classes[$order->status] ?? 'bg-gray-100 text-gray-800';
                return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full $class'>"
                    . ucfirst($order->status) . "</span>";
            })
            ->editColumn('created_at', function ($order) {
                return $order->created_at->format('M d, Y H:i'); // e.g. Jun 02, 2025 09:05
            })
            ->addColumn('actions', fn($order) => '<a href="' . route('orders.show', $order) . '" class="text-green-600 hover:text-green-900">View</a>')
            ->rawColumns(['status', 'actions'])
            //->removeColumn('id')
            ->make(true);
    }

    // Show create order form
    public function create()
    {
        $products = Product::all();
        $users = User::all();
        return view('orders.create', compact('products', 'users'));
    }

    // Store new order
    public function store(Request $request)
    {
        // ✅ Implement your validation & create logic
    }

    // Show order details
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.vendor']);
        return view('orders.show', compact('order'));
    }

    // Update order status
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $this->orderService->markAsCompleted($order->id);
    
            return redirect()->back()
                ->with('success', 'Order completed successfully!');
        } catch (Exception $e) {      
            return redirect()->back()
                ->with('error', "Failed to complete order: " . $e->getMessage());
        }
    }

    // Delete order
    public function destroy(Order $order)
    {
        try{
            $order->update(['status', StatusEnum::CANCELLED()]);
            return redirect()->back()
                ->with('success', 'Order cancelled successfully');
        } catch (Exception $e) {      
            return redirect()->back()
                ->with('error', "Failed to complete order: " . $e->getMessage());
        }
       
    }
}
