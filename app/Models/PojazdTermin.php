<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PojazdTermin extends Model
{
    protected $table = 'pojazdy_terminy';

    protected $fillable = ['nr_rej', 'rodzaj', 'marka', 'wlasciciel', 'vin', 'rok_prod', 'opis'];

    public function akcje()
    {
        return $this->hasMany(PojazdTerminAkcja::class, 'pojazd_id');
    }

    public function nadchodzaceAkcje(int $days = 30)
    {
        return $this->akcje()
            ->whereNotNull('deadline_date')
            ->where('deadline_date', '<=', now()->addDays($days))
            ->orderBy('deadline_date');
    }
}
