<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'karenderia_id',
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'ingredients',
        'allergens',
        'is_available',
        'preparation_time',
        'calories',
        'spicy_level'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'allergens' => 'array',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'calories' => 'integer',
        'preparation_time' => 'integer',
        'spicy_level' => 'integer'
    ];

    /**
     * Get the karenderia that owns the menu item
     */
    public function karenderia()
    {
        return $this->belongsTo(Karenderia::class, 'karenderia_id')->withDefault();
    }
}
