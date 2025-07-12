<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Get all orders
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('karenderia')->paginate(20);

        return response()->json([
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total()
            ]
        ]);
    }

    /**
     * Create new order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'karenderiaId' => 'required|exists:karenderias,id',
            'items' => 'required|array|min:1',
            'customerName' => 'string|max:255',
            'customerPhone' => 'string|max:20',
            'orderType' => 'required|in:dine-in,takeout,delivery',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'totalAmount' => 'required|numeric|min:0',
            'paymentMethod' => 'required|in:cash,card,gcash,maya',
            'notes' => 'string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::create([
            'karenderia_id' => $request->karenderiaId,
            'customer_name' => $request->customerName,
            'customer_phone' => $request->customerPhone,
            'order_type' => $request->orderType,
            'subtotal' => $request->subtotal,
            'tax' => $request->tax,
            'discount' => $request->discount ?? 0,
            'total_amount' => $request->totalAmount,
            'payment_method' => $request->paymentMethod,
            'order_status' => 'pending',
            'notes' => $request->notes,
            'order_number' => Order::generateOrderNumber(),
            'items' => $request->items
        ]);

        return response()->json([
            'data' => [
                'id' => $order->id
            ]
        ], 201);
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request): JsonResponse
    {
        $orders = Order::with('karenderia')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Get specific order
     */
    public function show($id): JsonResponse
    {
        $order = Order::with('karenderia')->findOrFail($id);

        return response()->json([
            'data' => $order
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($id);
        $order->update(['order_status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
