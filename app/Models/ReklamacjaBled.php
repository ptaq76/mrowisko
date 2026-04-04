<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReklamacjaBled extends Model
{
    protected $table = 'reklamacje_bledy';

    protected $fillable = [
        'mail_subject',
        'mail_date',
        'blad',
        'plik_1',
        'plik_2',
        'folder_temp',
        'status',
    ];

    protected $casts = [
        'mail_date' => 'datetime',
    ];
}
