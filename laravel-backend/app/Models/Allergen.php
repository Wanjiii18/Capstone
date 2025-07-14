<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'severity',
        'notes'
    ];

    protected $casts = [
        'severity' => 'string'
    ];

    /**
     * Get the user that owns the allergen
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}