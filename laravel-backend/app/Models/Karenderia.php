<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karenderia extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'business_name',
        'business_address',
        'contact_number',
        'description',
        'cuisine',
        'operating_hours',
        'social_media_links',
        'estimated_capacity',
        'price_range',
        'latitude',
        'longitude',
        'is_verified',
        'is_active',
        'rating',
        'image_url'
    ];

    protected $casts = [
        'cuisine' => 'array',
        'operating_hours' => 'array',
        'social_media_links' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:1'
    ];

    /**
     * Get the owner of the karenderia
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
