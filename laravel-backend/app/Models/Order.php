<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'karenderia_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'tax',
        'total_amount',
        'total_cost',
        'delivery_address',
        'delivery_coordinates',
        'special_instructions',
        'estimated_delivery_time',
        'actual_delivery_time',
        'order_tracking',
        'customer_rating',
        'customer_review'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'delivery_coordinates' => 'array',
        'order_tracking' => 'array',
        'customer_rating' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function karenderia(): BelongsTo
    {
        return $this->belongsTo(Karenderia::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope for paid orders
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Calculate profit for this order
    public function getProfitAttribute()
    {
        if (!$this->total_cost) {
            return 0;
        }
        
        return $this->total_amount - $this->total_cost;
    }

    // Generate unique order number
    public static function generateOrderNumber()
    {
        $prefix = 'KP';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return $prefix . $timestamp . $random;
    }

    // Boot method to auto-generate order number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }
}
