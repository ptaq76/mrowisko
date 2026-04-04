<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reklamacja extends Model
{
    protected $table = 'reklamacje';

    protected $fillable = [
        'typ',
        'lieferschein',
        'lieferschein_id',
        'masa_netto',
        'mail_subject',
        'mail_date',
        'plik_lieferschein',
        'plik_masa',
        'sciezka_pliku_masy',
    ];

    protected $casts = [
        'mail_date'  => 'datetime',
        'masa_netto' => 'decimal:3',
    ];

    public function lieferscheinModel()
    {
        return $this->belongsTo(Lieferschein::class, 'lieferschein_id');
    }
}
