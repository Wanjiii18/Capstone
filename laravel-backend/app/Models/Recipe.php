<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'karenderia_id',
        'ingredients',
        'instructions',
        'prep_time_minutes',
        'cook_time_minutes',
        'difficulty_level',
        'servings',
        'category',
        'cuisine_type',
        'cost_estimate',
        'nutritional_info',
        'image_url',
        'is_published',
        'is_signature',
        'rating',
        'total_reviews',
        'times_cooked'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'nutritional_info' => 'array',
        'cost_estimate' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_published' => 'boolean',
        'is_signature' => 'boolean'
    ];

    /**
     * Get the karenderia that owns this recipe
     */
    public function karenderia(): BelongsTo
    {
        return $this->belongsTo(Karenderia::class);
    }

    /**
     * Get menu items created from this recipe
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * Calculate total cooking time
     */
    public function getTotalTimeAttribute(): int
    {
        return $this->prep_time_minutes + $this->cook_time_minutes;
    }

    /**
     * Get difficulty color for UI
     */
    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'success',
            'medium' => 'warning',
            'hard' => 'danger',
            default => 'medium'
        };
    }

    /**
     * Get cost per serving
     */
    public function getCostPerServingAttribute(): float
    {
        return $this->servings > 0 ? $this->cost_estimate / $this->servings : 0;
    }

    /**
     * Check if recipe has enough details to create menu item
     */
    public function canCreateMenuItem(): bool
    {
        return !empty($this->name) &&
               !empty($this->description) &&
               !empty($this->ingredients) &&
               !empty($this->instructions) &&
               $this->cost_estimate > 0 &&
               $this->servings > 0;
    }

    /**
     * Scope for published recipes only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for signature/specialty recipes
     */
    public function scopeSignature($query)
    {
        return $query->where('is_signature', true);
    }

    /**
     * Scope by difficulty level
     */
    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
