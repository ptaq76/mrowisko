<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annex7WasteDescription extends Model
{
    protected $fillable = ['description'];

    public function shipments(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'waste_description_id');
    }
}
