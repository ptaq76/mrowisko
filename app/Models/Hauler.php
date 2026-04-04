<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hauler extends Model
{
    protected $fillable = ['client_id', 'sort_order'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
