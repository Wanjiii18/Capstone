<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karenderia extends Model
{
    use HasFactory;

    protected $table = 'karenderias';

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'owner_id',
        'rating',
        'is_open',
        'opening_hours',
        'image_url',
        'cuisine_type',
        'price_range',
        'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'rating' => 'decimal:2',
        'is_open' => 'boolean',
        'opening_hours' => 'array',
        'cuisine_type' => 'array',
        'price_range' => 'array'
    ];

    /**
     * Get the owner (user) of the karenderia
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the menu items for this karenderia
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * Get the orders for this karenderia
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}