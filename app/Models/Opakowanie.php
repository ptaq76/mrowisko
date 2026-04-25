<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opakowanie extends Model
{
    protected $table = 'opakowania';

    protected $fillable = [
        'name',
        'waga',
        'is_active',
    ];

    protected $casts = [
        'waga'      => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
