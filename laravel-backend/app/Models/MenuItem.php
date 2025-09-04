<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'karenderia_id',
        'recipe_id',
        'name',
        'description',
        'price',
        'cost_price',
        'category',
        'image_url',
        'images',
        'is_available',
        'is_featured',
        'preparation_time_minutes',
        'preparation_time',
        'calories',
        'ingredients',
        'allergens',
        'dietary_info',
        'spice_level',
        'serving_size',
        'average_rating',
        'total_reviews',
        'total_orders'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'images' => 'array',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'ingredients' => 'array',
        'allergens' => 'array',
        'average_rating' => 'decimal:2'
    ];

    public function karenderia(): BelongsTo
    {
        return $this->belongsTo(Karenderia::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the favorites for this menu item
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the reviews for this menu item
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the meal history entries for this menu item
     */
    public function mealHistory(): HasMany
    {
        return $this->hasMany(MealHistory::class);
    }

    // Scope for available items
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // Scope for featured items
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Calculate profit margin
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        
        return (($this->price - $this->cost_price) / $this->price) * 100;
    }

    // Calculate total revenue for this item
    public function getTotalRevenueAttribute()
    {
        return $this->orderItems()
            ->whereHas('order', function($query) {
                $query->where('payment_status', 'paid');
            })
            ->sum('total_price');
    }

    // Calculate total profit for this item
    public function getTotalProfitAttribute()
    {
        return $this->orderItems()
            ->whereHas('order', function($query) {
                $query->where('payment_status', 'paid');
            })
            ->whereNotNull('total_cost')
            ->selectRaw('SUM(total_price - total_cost) as profit')
            ->value('profit') ?? 0;
    }
}
