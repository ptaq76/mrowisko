<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KosztTransportu extends Model
{
    protected $table    = 'koszty_transportu';
    protected $fillable = ['start_id', 'stop_id', 'przewoznik_id', 'cena_eur', 'is_active'];
    protected $casts    = ['cena_eur' => 'decimal:2', 'is_active' => 'boolean'];

    public function start()
    {
        return $this->belongsTo(Client::class, 'start_id');
    }

    public function stop()
    {
        return $this->belongsTo(Client::class, 'stop_id');
    }

    public function przewoznik()
    {
        return $this->belongsTo(Przewoznik::class);
    }
}
