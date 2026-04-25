<?php

namespace App\Models\Karchem;

use Illuminate\Database\Eloquent\Model;

class KarchemWysylki extends Model
{
    protected $table = 'karchem_wysylki';

    protected $fillable = [
        'data',
        'kod',
        'ilosc',
        'klient',
    ];

    protected $casts = [
        'data'  => 'date',
        'ilosc' => 'decimal:3',
    ];
}
