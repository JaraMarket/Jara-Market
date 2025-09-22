<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Order;
use Illuminate\Http\Response;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Exceptions\GeneralException;
use App\Http\Resources\OrderResource;
use App\Http\Requests\DecideOrderItemRequest;
use App\Http\Resources\IngredientOrderResource;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService)
    { }

    public function store(OrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request);
            return response()->success('Order created successfully', new OrderResource($order), 201);
            
        } catch (GeneralException $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel(Order $order)
    {
        try {
            $order = $this->orderService->cancelOrder($order);
            return response()->success('Order cancelled successfully', new OrderResource($order), 201);
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function show(Order $order)
    {
        try {
            $order = $this->orderService->getOrderById($order->id);
            return response()->success('Order retrieved successfully', new OrderResource($order), 201);
            
        } catch (GeneralException $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function all()
    {
        try {
            $order = $this->orderService->all();
            return response()->success('Order retrieved successfully', OrderResource::collection($order), 201);
            
        } catch (GeneralException $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAvailableOrders()
    {
        try {
            $order = $this->orderService->getAvailableOrders();
            
            return response()->success('Order retrieved successfully', IngredientOrderResource::collection($order), 200);
            
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showOrderByItemId($item_id)
    {
        try {
            $order = $this->orderService->showOrderByItemId($item_id);
            return response()->success('Order retrieved successfully', new IngredientOrderResource($order), 200);
            
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function decide(DecideOrderItemRequest $request, $item_id)
    {
        try {
            $order = $this->orderService->decide($request->validated(), $item_id);
            return response()->success('Action taken successfully', new IngredientOrderResource($order), 200);
            
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myOrders()
    {
        try {
            $order = $this->orderService->getMyOrders();
            return response()->success('Order retrieved successfully', IngredientOrderResource::collection($order), 200);
            
        } catch (Exception $e) {
            report($e);

            return response()->errorResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
