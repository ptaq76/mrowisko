<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KarchemKlienci extends Model
{
    protected $table = 'karchem_klienci';

    protected $fillable = [
        'nip',
        'nazwa',
    ];
}
