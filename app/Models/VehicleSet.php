<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSet extends Model
{
    protected $fillable = ['label', 'tractor_id', 'trailer_id', 'tare_kg', 'is_active'];

    protected $casts = [
        'tare_kg'   => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function tractor()
    {
        return $this->belongsTo(Vehicle::class, 'tractor_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Vehicle::class, 'trailer_id');
    }

    // Znajdź zestaw dla ciągnika i naczepy
    public static function findForVehicles(?int $tractorId, ?int $trailerId): ?self
    {
        return self::where('tractor_id', $tractorId)
            ->where('trailer_id', $trailerId)
            ->where('is_active', true)
            ->first();
    }
}
