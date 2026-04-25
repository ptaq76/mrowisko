<?php

namespace App\Models\Karchem;

use Illuminate\Database\Eloquent\Model;

class KarchemStanPoczatkowy extends Model
{
    protected $table = 'karchem_stany_poczatkowe';

    protected $fillable = [
        'rok',
        'kod',
        'ilosc',
    ];

    protected $casts = [
        'rok'   => 'integer',
        'ilosc' => 'decimal:3',
    ];
}
