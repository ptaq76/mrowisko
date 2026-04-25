<?php

namespace App\Models\Karchem;

use Illuminate\Database\Eloquent\Model;

class KarchemKodyOdpadow extends Model
{
    protected $table = 'karchem_kody_odpadow';

    protected $fillable = [
        'kod',
    ];
}
