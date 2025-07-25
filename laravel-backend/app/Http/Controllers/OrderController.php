<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Build query based on user role
            $query = \App\Models\Order::with(['orderItems.menuItem', 'karenderia', 'customer']);
            
            if ($user) {
                if ($user->role === 'admin') {
                    // Admin can see all orders
                } elseif ($user->role === 'karenderia_owner') {
                    // Karenderia owner can see orders for their karenderias
                    $karenderiaIds = \App\Models\Karenderia::where('owner_id', $user->id)->pluck('id');
                    $query->whereIn('karenderia_id', $karenderiaIds);
                } else {
                    // Customers can see their own orders
                    $query->where('customer_id', $user->id);
                }
            } else {
                // Guest users can't see orders
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }
            
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('karenderia_id')) {
                $query->where('karenderia_id', $request->karenderia_id);
            }
            
            // Order by most recent first
            $orders = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 20));
            
            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to fetch orders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'karenderiaId' => 'required|exists:karenderias,id',
                'items' => 'required|array|min:1',
                'items.*.menuItemId' => 'required|string',
                'items.*.menuItemName' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unitPrice' => 'required|numeric|min:0',
                'items.*.subtotal' => 'required|numeric|min:0',
                'customerName' => 'nullable|string|max:255',
                'customerPhone' => 'nullable|string|max:20',
                'orderType' => 'required|in:dine-in,takeout,delivery',
                'subtotal' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'totalAmount' => 'required|numeric|min:0',
                'paymentMethod' => 'required|in:cash,card,gcash,online_payment',
                'notes' => 'nullable|string',
                'seasonalData' => 'nullable|array'
            ]);

            // Get the authenticated user (if any) - allow guest orders
            $user = $request->user();
            
            // Create the order
            $order = \App\Models\Order::create([
                'customer_id' => $user ? $user->id : null,
                'karenderia_id' => $validatedData['karenderiaId'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $validatedData['paymentMethod'],
                'subtotal' => $validatedData['subtotal'],
                'delivery_fee' => 0, // Set based on order type if needed
                'service_fee' => 0,
                'tax' => $validatedData['tax'] ?? 0,
                'total_amount' => $validatedData['totalAmount'],
                'total_cost' => 0, // Will be calculated based on menu items
                'delivery_address' => $validatedData['orderType'] === 'delivery' ? ($user && $user->address ? $user->address : null) : null,
                'special_instructions' => $validatedData['notes'] ?? null,
                'estimated_delivery_time' => $validatedData['orderType'] === 'delivery' ? now()->addMinutes(30) : null,
                'order_tracking' => [
                    'status' => 'pending',
                    'created_at' => now()->toISOString(),
                    'customer_name' => $validatedData['customerName'] ?? ($user->name ?? 'Guest'),
                    'customer_phone' => $validatedData['customerPhone'] ?? ($user->phone_number ?? null),
                    'order_type' => $validatedData['orderType'],
                    'seasonal_data' => $validatedData['seasonalData'] ?? null
                ]
            ]);

            // Create order items
            $totalCost = 0;
            foreach ($validatedData['items'] as $item) {
                // Try to find the menu item to get cost price
                $menuItem = \App\Models\MenuItem::where('id', $item['menuItemId'])
                    ->orWhere('name', $item['menuItemName'])
                    ->first();
                
                $unitCost = $menuItem ? $menuItem->cost_price : ($item['unitPrice'] * 0.6); // Default to 60% margin
                $itemTotalCost = $unitCost * $item['quantity'];
                $totalCost += $itemTotalCost;

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem ? $menuItem->id : null,
                    'menu_item_name' => $item['menuItemName'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice'],
                    'unit_cost' => $unitCost,
                    'total_price' => $item['subtotal'],
                    'total_cost' => $itemTotalCost,
                    'special_instructions' => null,
                    'preparation_time_minutes' => $menuItem ? $menuItem->preparation_time_minutes : 15
                ]);
            }

            // Update the order with total cost
            $order->update(['total_cost' => $totalCost]);

            // Load the order with relationships
            $order->load(['orderItems', 'karenderia', 'customer']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order' => $order
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'orders' => []
        ]);
    }

    /**
     * Display the specified order
     */
    public function show(Request $request, $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Order details not implemented yet'
        ], 501);
    }

    /**
     * Update the specified order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'orderStatus' => 'required|in:pending,preparing,ready,completed,cancelled',
                'preparedAt' => 'nullable|date',
                'completedAt' => 'nullable|date'
            ]);

            $order = \App\Models\Order::findOrFail($id);
            
            // Check if user has permission to update this order
            $user = $request->user();
            if ($user->role !== 'admin') {
                if ($user->role === 'karenderia_owner') {
                    $karenderiaIds = \App\Models\Karenderia::where('owner_id', $user->id)->pluck('id');
                    if (!$karenderiaIds->contains($order->karenderia_id)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to update this order'
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to update orders'
                    ], 403);
                }
            }

            // Update order status
            $order->status = $validatedData['orderStatus'];
            
            // Update order tracking with timestamps
            $tracking = $order->order_tracking ?? [];
            $tracking['status'] = $validatedData['orderStatus'];
            $tracking['updated_at'] = now()->toISOString();
            
            if ($validatedData['orderStatus'] === 'preparing') {
                $tracking['prepared_at'] = $validatedData['preparedAt'] ?? now()->toISOString();
            } elseif ($validatedData['orderStatus'] === 'completed') {
                $tracking['completed_at'] = $validatedData['completedAt'] ?? now()->toISOString();
                $order->payment_status = 'paid'; // Automatically mark as paid when completed
            }
            
            $order->order_tracking = $tracking;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order->fresh(['orderItems.menuItem', 'karenderia', 'customer'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order status update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status'
            ], 500);
        }
    }
}
