<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'breakfast',
        'lunch',
        'dinner',
        'plan_date'
    ];

    protected $casts = [
        'meals' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the user that owns the meal plan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}