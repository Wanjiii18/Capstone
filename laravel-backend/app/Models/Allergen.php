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

    /**
     * Get the user who owns this allergen
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
