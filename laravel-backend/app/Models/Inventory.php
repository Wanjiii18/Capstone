<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'karenderia_id',
        'item_name',
        'description',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'unit_cost',
        'total_value',
        'supplier',
        'last_restocked',
        'expiry_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'maximum_stock' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'last_restocked' => 'date',
        'expiry_date' => 'date'
    ];

    public function karenderia(): BelongsTo
    {
        return $this->belongsTo(Karenderia::class);
    }

    // Scope for low stock items
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    // Scope for out of stock items
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    // Scope for expired items
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now()->toDateString());
    }

    // Scope for expiring soon (within 7 days)
    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('expiry_date', [
            now()->toDateString(),
            now()->addDays(7)->toDateString()
        ]);
    }

    // Auto-calculate total value and update status
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($inventory) {
            // Calculate total value
            $inventory->total_value = $inventory->current_stock * $inventory->unit_cost;
            
            // Update status based on stock levels and expiry
            if ($inventory->expiry_date && $inventory->expiry_date < now()->toDateString()) {
                $inventory->status = 'expired';
            } elseif ($inventory->current_stock <= 0) {
                $inventory->status = 'out_of_stock';
            } elseif ($inventory->current_stock <= $inventory->minimum_stock) {
                $inventory->status = 'low_stock';
            } else {
                $inventory->status = 'available';
            }
        });
    }
}
