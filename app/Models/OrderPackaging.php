<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPackaging extends Model
{
    protected $table = 'order_packaging';

    protected $fillable = [
        'order_id',
        'opakowanie_id',
        'quantity',
        'qty_plac',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function opakowanie()
    {
        return $this->belongsTo(Opakowanie::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirmed_by');
    }

    /**
     * Czy ten wpis został już potwierdzony przez plac?
     */
    public function isConfirmedByPlac(): bool
    {
        return $this->confirmed_at !== null;
    }
}