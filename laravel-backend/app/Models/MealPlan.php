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
        'duration',
        'calories_per_day',
        'type',
        'start_date',
        'end_date',
        'is_active',
        'meals'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'meals' => 'array'
    ];

    /**
     * Get the user who owns this meal plan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
