<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'karenderia_id',
        'customer_name',
        'customer_phone',
        'order_type',
        'subtotal',
        'tax',
        'discount',
        'total_amount',
        'payment_method',
        'order_status',
        'notes',
        'order_number',
        'items'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'items' => 'array'
    ];

    /**
     * Get the karenderia for this order
     */
    public function karenderia()
    {
        return $this->belongsTo(Karenderia::class);
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
