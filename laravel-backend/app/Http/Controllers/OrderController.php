<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Karenderia;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    /**
     * Store a newly created order (POS / dine-in sales)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'karenderiaId' => 'required|exists:karenderias,id',
                'items' => 'required|array|min:1',
                'items.*.menuItemId' => 'required',
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
                'paymentMethod' => 'required|in:cash,card,gcash,maya,online_payment',
                'orderStatus' => 'nullable|string',
                'tableNumber' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'seasonalData' => 'nullable|array',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $karenderiaId = (int) $validatedData['karenderiaId'];

            if ($user->role === 'karenderia_owner') {
                $ownsKarenderia = Karenderia::where('id', $karenderiaId)
                    ->where('owner_id', $user->id)
                    ->exists();

                if (!$ownsKarenderia) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized for this karenderia',
                    ], 403);
                }
            }

            $status = $this->normalizeOrderStatus($validatedData['orderStatus'] ?? 'delivered');
            $paymentStatus = in_array($status, ['delivered', 'ready'], true) ? 'paid' : 'pending';
            $paymentMethod = $validatedData['paymentMethod'] === 'online_payment'
                ? 'gcash'
                : $validatedData['paymentMethod'];

            $order = DB::transaction(function () use ($validatedData, $user, $karenderiaId, $status, $paymentStatus, $paymentMethod) {
                $order = Order::create([
                    'order_number' => 'KP-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
                    'customer_id' => $user->id,
                    'karenderia_id' => $karenderiaId,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'subtotal' => $validatedData['subtotal'],
                    'delivery_fee' => 0,
                    'service_fee' => 0,
                    'tax' => $validatedData['tax'] ?? 0,
                    'total_amount' => $validatedData['totalAmount'],
                    'total_cost' => 0,
                    'delivery_address' => $validatedData['orderType'] === 'delivery'
                        ? ($user->address ?? null)
                        : null,
                    'special_instructions' => $validatedData['notes'] ?? null,
                    'estimated_delivery_time' => $validatedData['orderType'] === 'delivery'
                        ? now()->addMinutes(30)
                        : null,
                    'order_tracking' => [
                        'status' => $status,
                        'created_at' => now()->toISOString(),
                        'customer_name' => $validatedData['customerName'] ?? $user->name,
                        'customer_phone' => $validatedData['customerPhone'] ?? ($user->phone_number ?? null),
                        'order_type' => $validatedData['orderType'],
                        'table_number' => $validatedData['tableNumber'] ?? null,
                        'seasonal_data' => $validatedData['seasonalData'] ?? null,
                        'stock_deducted' => false,
                    ],
                ]);

                $totalCost = 0;

                foreach ($validatedData['items'] as $item) {
                    $menuItemId = $this->resolveMenuItemId($karenderiaId, $item);
                    if (!$menuItemId) {
                        throw new \RuntimeException('Menu item not found: ' . $item['menuItemName']);
                    }

                    $menuItem = MenuItem::where('id', $menuItemId)
                        ->where('karenderia_id', $karenderiaId)
                        ->first();

                    $unitCost = $menuItem
                        ? (float) ($menuItem->cost_price ?? ($item['unitPrice'] * 0.6))
                        : ((float) $item['unitPrice'] * 0.6);
                    $itemTotalCost = $unitCost * (int) $item['quantity'];
                    $totalCost += $itemTotalCost;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItemId,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unitPrice'],
                        'unit_cost' => $unitCost,
                        'total_price' => $item['subtotal'],
                        'total_cost' => $itemTotalCost,
                    ]);

                    MenuItem::where('id', $menuItemId)->increment('total_orders', (int) $item['quantity']);
                }

                $order->update(['total_cost' => $totalCost]);

                if (in_array($status, ['delivered', 'ready'], true)) {
                    $this->deductKitchenStockForCompletedOrder((int) $order->id, $karenderiaId);

                    $tracking = $order->order_tracking ?? [];
                    $tracking['stock_deducted'] = true;
                    $tracking['completed_at'] = now()->toISOString();
                    $order->update(['order_tracking' => $tracking]);
                }

                return $order->fresh(['orderItems', 'karenderia', 'customer']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully. Inventory and analytics updated.',
                'data' => [
                    'id' => (string) $order->id,
                    'order_number' => $order->order_number,
                    'order' => $order,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function normalizeOrderStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        if ($status === 'completed') {
            return 'delivered';
        }

        $allowed = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];

        return in_array($status, $allowed, true) ? $status : 'delivered';
    }

    private function resolveMenuItemId(int $karenderiaId, array $item): ?int
    {
        $menuItemId = $item['menuItemId'] ?? null;

        if (is_numeric($menuItemId)) {
            $exists = MenuItem::where('id', (int) $menuItemId)
                ->where('karenderia_id', $karenderiaId)
                ->exists();

            return $exists ? (int) $menuItemId : null;
        }

        $menuItem = MenuItem::where('karenderia_id', $karenderiaId)
            ->where('name', $item['menuItemName'])
            ->first();

        return $menuItem?->id;
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }

            $limit = min(max((int) $request->get('limit', 30), 1), 100);
            $period = $request->get('period', 'today');

            $query = Order::with(['orderItems.menuItem'])
                ->where('payment_status', 'paid')
                ->orderByDesc('created_at');

            if ($period === 'today') {
                $query->whereDate('created_at', today());
            }

            if ($user->role === 'karenderia_owner') {
                $karenderiaIds = Karenderia::where('owner_id', $user->id)->pluck('id');
                $query->whereIn('karenderia_id', $karenderiaIds);
            } elseif ($user->role !== 'admin') {
                $query->where('customer_id', $user->id);
            }

            return response()->json([
                'success' => true,
                'data' => $query->limit($limit)->get(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch recent orders: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent orders',
            ], 500);
        }
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
                'orderStatus' => 'required|in:pending,preparing,ready,completed,cancelled,confirmed,delivered',
                'preparedAt' => 'nullable|date',
                'completedAt' => 'nullable|date'
            ]);

            $order = Order::findOrFail($id);
            
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

            $previousStatus = $order->status;
            $newStatus = $this->normalizeOrderStatus($validatedData['orderStatus']);

            DB::transaction(function () use ($order, $validatedData, $previousStatus, $newStatus) {
                $order->status = $newStatus;

                $tracking = $order->order_tracking ?? [];
                $tracking['status'] = $newStatus;
                $tracking['updated_at'] = now()->toISOString();

                if ($newStatus === 'preparing') {
                    $tracking['prepared_at'] = $validatedData['preparedAt'] ?? now()->toISOString();
                } elseif (in_array($newStatus, ['delivered', 'ready'], true)) {
                    $tracking['completed_at'] = $validatedData['completedAt'] ?? now()->toISOString();
                    $order->payment_status = 'paid';
                }

                $order->order_tracking = $tracking;
                $order->save();

                $alreadyDeducted = (bool) ($tracking['stock_deducted'] ?? false);
                $isSaleComplete = in_array($newStatus, ['delivered', 'ready'], true);
                $wasSaleComplete = in_array($previousStatus, ['delivered', 'ready'], true);

                if ($isSaleComplete && !$wasSaleComplete && !$alreadyDeducted) {
                    $this->deductKitchenStockForCompletedOrder((int) $order->id, (int) $order->karenderia_id);
                    $tracking['stock_deducted'] = true;
                    $order->update(['order_tracking' => $tracking]);
                }
            });

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
            Log::error('Order status update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status'
            ], 500);
        }
    }

    private function deductKitchenStockForCompletedOrder(int $orderId, int $karenderiaId): void
    {
        $orderItems = DB::table('order_items')
            ->where('order_id', $orderId)
            ->get(['menu_item_id', 'quantity']);

        $menuItemNames = DB::table('order_items')
            ->join('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->where('order_items.order_id', $orderId)
            ->pluck('menu_items.name', 'order_items.menu_item_id');

        foreach ($orderItems as $orderItem) {
            $orderQuantity = (float) $orderItem->quantity;
            if ($orderQuantity <= 0) {
                continue;
            }

            $deductedViaIngredients = false;

            if (!empty($orderItem->menu_item_id)) {
                $menuItem = DB::table('menu_items')
                    ->where('id', $orderItem->menu_item_id)
                    ->first(['ingredients']);

                if ($menuItem && !empty($menuItem->ingredients)) {
                    $ingredients = is_array($menuItem->ingredients)
                        ? $menuItem->ingredients
                        : json_decode((string) $menuItem->ingredients, true);

                    if (is_array($ingredients) && count($ingredients) > 0) {
                        foreach ($ingredients as $ingredient) {
                            $ingredientName = $this->resolveIngredientName($ingredient);
                            if (!$ingredientName) {
                                continue;
                            }

                            $perOrderUsage = $this->resolveIngredientUsage($ingredient);
                            $totalUsage = $perOrderUsage * $orderQuantity;
                            $this->decrementInventoryItem($karenderiaId, $ingredientName, $totalUsage);
                            $deductedViaIngredients = true;
                        }
                    }
                }
            }

            // Fallback: use menu item name directly when no structured ingredient data exists.
            $fallbackName = $orderItem->menu_item_id ? ($menuItemNames[$orderItem->menu_item_id] ?? null) : null;
            if (!$deductedViaIngredients && is_string($fallbackName) && trim($fallbackName) !== '') {
                $this->decrementInventoryItem($karenderiaId, trim($fallbackName), $orderQuantity);
            }
        }
    }

    private function resolveIngredientName($ingredient): ?string
    {
        if (is_string($ingredient)) {
            return trim($ingredient) !== '' ? trim($ingredient) : null;
        }

        if (is_array($ingredient)) {
            $name = $ingredient['ingredientName'] ?? $ingredient['name'] ?? $ingredient['ingredient'] ?? $ingredient['item_name'] ?? null;
            if (is_string($name) && trim($name) !== '') {
                return trim($name);
            }
        }

        return null;
    }

    private function resolveIngredientUsage($ingredient): float
    {
        if (!is_array($ingredient)) {
            return 1.0;
        }

        $usage = $ingredient['quantity'] ?? $ingredient['amount'] ?? $ingredient['qty'] ?? 1;
        $usage = (float) $usage;

        return $usage > 0 ? $usage : 1.0;
    }

    private function decrementInventoryItem(int $karenderiaId, string $itemName, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $normalizedName = mb_strtolower(trim($itemName));

        $inventoryItem = Inventory::where('karenderia_id', $karenderiaId)
            ->whereRaw('LOWER(item_name) = ?', [$normalizedName])
            ->first();

        if (!$inventoryItem) {
            return;
        }

        $current = (float) $inventoryItem->current_stock;
        $inventoryItem->current_stock = max(0, $current - $amount);
        $inventoryItem->save();
    }
}
