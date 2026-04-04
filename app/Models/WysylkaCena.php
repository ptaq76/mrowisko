<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WysylkaCena extends Model
{
    protected $table    = 'wysylki_ceny';
    protected $fillable = ['order_id', 'cena_eur'];
    protected $casts    = ['cena_eur' => 'decimal:2'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
