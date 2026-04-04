<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderQuickButton extends Model
{
    protected $fillable = ['label', 'type', 'sort', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    const TYPES = [
        'goods' => 'Towary',
        'notes' => 'Uwagi',
    ];

    public function scopeGoods($query)
    {
        return $query->where('type', 'goods')->where('is_active', true)->orderBy('sort');
    }

    public function scopeNotes($query)
    {
        return $query->where('type', 'notes')->where('is_active', true)->orderBy('sort');
    }
}
