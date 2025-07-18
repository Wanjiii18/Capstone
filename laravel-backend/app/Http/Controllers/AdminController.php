<?php

namespace App\Http\Controllers;

use App\Models\Karenderia;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
            }
            return $next($request);
        });
    }

    /**
     * Get dashboard overview
     */
    public function dashboard()
    {
        $totalKarenderias = Karenderia::count();
        $activeKarenderias = Karenderia::where('status', 'active')->count();
        $pendingKarenderias = Karenderia::where('status', 'pending')->count();
        
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalProfit = Order::where('payment_status', 'paid')
            ->whereNotNull('total_cost')
            ->selectRaw('SUM(total_amount - total_cost) as profit')
            ->value('profit') ?? 0;
        
        $totalOrders = Order::count();
        $todaysOrders = Order::whereDate('created_at', today())->count();
        
        $totalUsers = User::where('role', 'customer')->count();
        $totalOwners = User::where('role', 'karenderia_owner')->count();

        return response()->json([
            'overview' => [
                'total_karenderias' => $totalKarenderias,
                'active_karenderias' => $activeKarenderias,
                'pending_karenderias' => $pendingKarenderias,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'total_orders' => $totalOrders,
                'todays_orders' => $todaysOrders,
                'total_customers' => $totalUsers,
                'total_owners' => $totalOwners,
            ]
        ]);
    }

    /**
     * Get all karenderias with their stats
     */
    public function karenderias(Request $request)
    {
        $query = Karenderia::with(['owner:id,name,email'])
            ->withCount(['orders', 'menuItems']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or owner
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('owner', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $karenderias = $query->paginate($request->get('per_page', 15));

        // Add revenue and profit data
        $karenderias->getCollection()->transform(function ($karenderia) {
            $karenderia->total_revenue = $karenderia->orders()
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            $karenderia->total_profit = $karenderia->orders()
                ->where('payment_status', 'paid')
                ->whereNotNull('total_cost')
                ->selectRaw('SUM(total_amount - total_cost) as profit')
                ->value('profit') ?? 0;

            $karenderia->monthly_revenue = $karenderia->orders()
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');

            return $karenderia;
        });

        return response()->json($karenderias);
    }

    /**
     * Get detailed karenderia information
     */
    public function karenderiaDetails($id)
    {
        $karenderia = Karenderia::with(['owner', 'menuItems', 'inventory'])
            ->findOrFail($id);

        // Sales data for the last 30 days
        $salesData = $karenderia->orders()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total_orders,
                SUM(total_amount) as daily_revenue,
                SUM(COALESCE(total_amount - total_cost, 0)) as daily_profit
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling items
        $topItems = MenuItem::where('karenderia_id', $id)
            ->withSum(['orderItems as total_sold' => function($query) {
                $query->whereHas('order', function($subQuery) {
                    $subQuery->where('payment_status', 'paid');
                });
            }], 'quantity')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Recent orders
        $recentOrders = Order::where('karenderia_id', $id)
            ->with(['customer:id,name,email', 'orderItems.menuItem:id,name,price'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Inventory alerts
        $lowStockItems = $karenderia->inventory()->lowStock()->count();
        $outOfStockItems = $karenderia->inventory()->outOfStock()->count();
        $expiringItems = $karenderia->inventory()->expiringSoon()->count();

        return response()->json([
            'karenderia' => $karenderia,
            'sales_data' => $salesData,
            'top_selling_items' => $topItems,
            'recent_orders' => $recentOrders,
            'inventory_alerts' => [
                'low_stock' => $lowStockItems,
                'out_of_stock' => $outOfStockItems,
                'expiring_soon' => $expiringItems
            ]
        ]);
    }

    /**
     * Get karenderia inventory
     */
    public function karenderiaInventory($id, Request $request)
    {
        $karenderia = Karenderia::findOrFail($id);
        
        $query = $karenderia->inventory();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by item name
        if ($request->has('search')) {
            $query->where('item_name', 'LIKE', "%{$request->search}%");
        }

        $inventory = $query->orderBy('item_name')->paginate($request->get('per_page', 20));

        return response()->json($inventory);
    }

    /**
     * Get sales analytics
     */
    public function salesAnalytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $karenderiaId = $request->get('karenderia_id');

        $query = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($karenderiaId) {
            $query->where('karenderia_id', $karenderiaId);
        }

        // Daily sales data
        $dailySales = $query->selectRaw('
            DATE(created_at) as date,
            COUNT(*) as total_orders,
            SUM(total_amount) as total_revenue,
            SUM(COALESCE(total_amount - total_cost, 0)) as total_profit,
            AVG(total_amount) as average_order_value
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Top performing karenderias
        $topKarenderias = Karenderia::withSum(['orders as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->withCount(['orders as total_orders' => function($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // Payment method breakdown
        $paymentMethods = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'daily_sales' => $dailySales,
            'top_karenderias' => $topKarenderias,
            'payment_methods' => $paymentMethods,
            'summary' => [
                'total_orders' => $query->count(),
                'total_revenue' => $query->sum('total_amount'),
                'total_profit' => $query->whereNotNull('total_cost')
                    ->selectRaw('SUM(total_amount - total_cost) as profit')
                    ->value('profit') ?? 0,
                'average_order_value' => $query->avg('total_amount')
            ]
        ]);
    }

    /**
     * Approve or reject karenderia application
     */
    public function updateKarenderiaStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string'
        ]);

        $karenderia = Karenderia::findOrFail($id);
        $karenderia->status = $request->status;
        $karenderia->save();

        // You can add notification logic here to inform the owner

        return response()->json([
            'message' => 'Karenderia status updated successfully',
            'karenderia' => $karenderia
        ]);
    }

    /**
     * Get inventory alerts across all karenderias
     */
    public function inventoryAlerts()
    {
        $lowStockItems = Inventory::with(['karenderia:id,name'])
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->take(50)
            ->get();

        $outOfStockItems = Inventory::with(['karenderia:id,name'])
            ->outOfStock()
            ->orderBy('current_stock', 'asc')
            ->take(50)
            ->get();

        $expiringItems = Inventory::with(['karenderia:id,name'])
            ->expiringSoon()
            ->orderBy('expiry_date', 'asc')
            ->take(50)
            ->get();

        $expiredItems = Inventory::with(['karenderia:id,name'])
            ->expired()
            ->orderBy('expiry_date', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'low_stock' => $lowStockItems,
            'out_of_stock' => $outOfStockItems,
            'expiring_soon' => $expiringItems,
            'expired' => $expiredItems
        ]);
    }

    /**
     * Get all users (customers and owners)
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->withCount(['mealPlans', 'karenderia'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($users);
    }
}
