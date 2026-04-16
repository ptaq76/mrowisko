<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annex7Contractor extends Model
{
    protected $fillable = [
        'name', 'short_name', 'address', 'contact', 'tel', 'mail', 'role',
    ];

    public function shipmentsAsArranger(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'arranger_id');
    }

    public function shipmentsAsImporter(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'importer_id');
    }

    public function shipmentsAsCarrier(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'carrier_id');
    }

    public function shipmentsAsGenerator(): HasMany
    {
        return $this->hasMany(Annex7Shipment::class, 'generator_id');
    }

    public function roleName(): string
    {
        return match ($this->role) {
            'arranger' => 'Pole 1 – Arranger',
            'importer' => 'Pole 2 – Importer / Consignee',
            'carrier' => 'Pole 5 – Carrier',
            'generator' => 'Pole 6 – Generator',
            default => $this->role,
        };
    }

    public function displayName(): string
    {
        return $this->short_name
            ? "{$this->short_name} ({$this->name})"
            : $this->name;
    }
}
