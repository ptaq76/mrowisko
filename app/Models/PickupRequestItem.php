<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequestItem extends Model
{
    protected $fillable = ['pickup_request_id', 'nazwa', 'cena', 'ilosc'];

    protected $casts = ['cena' => 'decimal:2'];

    public function request()
    {
        return $this->belongsTo(PickupRequest::class, 'pickup_request_id');
    }
}
