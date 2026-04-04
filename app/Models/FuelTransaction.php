<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelTransaction extends Model
{
    protected $fillable = [
        'type', 'liters', 'tank_after',
        'fuel_vehicle_id', 'operator', 'notes',
    ];

    public function vehicle()
    {
        return $this->belongsTo(FuelVehicle::class, 'fuel_vehicle_id');
    }

    // Aktualny stan zbiornika = tank_after ostatniej transakcji
    public static function currentLevel(): int
    {
        return (int) self::latest()->value('tank_after') ?? 0;
    }
}
