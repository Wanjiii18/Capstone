<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'meals',
        'is_active'
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