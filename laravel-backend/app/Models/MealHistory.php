<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealHistory extends Model
{
    use HasFactory;

    protected $table = 'meal_history';

    protected $fillable = [
        'user_id',
        'menu_item_id',
        'quantity',
        'order_id',
        'ordered_at',
        'rating',
        'review'
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'rating' => 'integer',
        'quantity' => 'integer'
    ];

    /**
     * Get the user that owns the meal history
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu item in the history
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the order associated with this history item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
