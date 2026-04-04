<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weighing extends Model
{
    protected $fillable = [
        'weighed_at', 'client_id', 'order_id',
        'plate1', 'plate2',
        'weight1', 'weight2', 'result',
        'goods', 'notes', 'source', 'is_archived', 'created_by_user',
    ];

    protected $casts = [
        'weighed_at' => 'datetime',
        'weight1'    => 'decimal:3',
        'weight2'    => 'decimal:3',
        'result'     => 'decimal:3',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Automatycznie oblicz wynik przy zapisie
    protected static function booted(): void
    {
        static::saving(function (self $w) {
            if ($w->weight1 !== null && $w->weight2 !== null) {
                $w->result = round((float)$w->weight1 - (float)$w->weight2, 3);
            }
        });
    }
}
