<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadingItem extends Model
{
    protected $table = 'loading_items';

    protected $fillable = [
        'order_id', 'fraction_id', 'bales', 'weight_kg', 'notes', 'operator_id',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function fraction()
    {
        return $this->belongsTo(WasteFraction::class, 'fraction_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
