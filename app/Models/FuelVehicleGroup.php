<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelVehicleGroup extends Model
{
    protected $table    = 'fuel_vehicle_groups';
    protected $fillable = ['nazwa', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function vehicles()
    {
        return $this->hasMany(FuelVehicle::class, 'grupa_id');
    }
}
