<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WysylkaTransport extends Model
{
    protected $table = 'wysylki_transport';

    protected $fillable = ['order_id', 'przewoznik_id', 'cena_eur', 'recznie'];

    protected $casts = ['cena_eur' => 'decimal:2', 'recznie' => 'boolean'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function przewoznik()
    {
        return $this->belongsTo(Przewoznik::class);
    }
}
