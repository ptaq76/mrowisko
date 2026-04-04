<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annex7RecoveryOperation extends Model
{
    protected $fillable = ['code', 'description'];

    public function shipments(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'recovery_operation_id');
    }
}
