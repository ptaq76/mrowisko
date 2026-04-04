<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate',
        'type',
        'subtype',
        'brand',
        'tare_kg',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tare_kg'   => 'decimal:2',
    ];

    const TYPES = [
        'ciągnik' => 'Ciągnik',
        'naczepa' => 'Naczepa',
        'solo'    => 'Solo',
    ];

    const SUBTYPES = [
        'hakowiec'     => 'Hakowiec',
        'firana'       => 'Firana',
        'walking_floor' => 'Walking Floor',
    ];

    public function typeName(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function subtypeName(): string
    {
        return self::SUBTYPES[$this->subtype] ?? '';
    }

    public function fullName(): string
    {
        $name = $this->plate;
        if ($this->subtype) $name .= ' (' . $this->subtypeName() . ')';
        return $name;
    }

    // Scopy
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTractors($query)
    {
        return $query->where('type', 'ciągnik');
    }

    public function scopeTrailers($query)
    {
        return $query->where('type', 'naczepa');
    }

    public function scopeSolo($query)
    {
        return $query->where('type', 'solo');
    }
}
