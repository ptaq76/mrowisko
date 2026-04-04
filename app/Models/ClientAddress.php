<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAddress extends Model
{
    protected $fillable = [
        'client_id',
        'city',
        'postal_code',
        'street',
        'hours',
        'notes',
        'distance_km',
        'latitude',
        'longitude',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function fullAddress(): string
    {
        return trim("{$this->postal_code} {$this->city}, {$this->street}");
    }
}
