<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'quantity',
        'unit_price',
        'unit_cost',
        'total_price',
        'total_cost',
        'special_instructions',
        'customizations',
        'preparation_time_minutes'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'customizations' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Calculate profit for this order item
    public function getProfitAttribute()
    {
        if (!$this->total_cost) {
            return 0;
        }
        
        return $this->total_price - $this->total_cost;
    }

    // Auto-calculate totals when creating/updating
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->quantity * $orderItem->unit_price;
            
            if ($orderItem->unit_cost) {
                $orderItem->total_cost = $orderItem->quantity * $orderItem->unit_cost;
            }
        });
    }
}
