<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelVehicle extends Model
{
    protected $table    = 'fuel_vehicles';
    protected $fillable = ['nazwa', 'grupa_id', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function group()
    {
        return $this->belongsTo(FuelVehicleGroup::class, 'grupa_id');
    }

    public function transactions()
    {
        return $this->hasMany(FuelTransaction::class);
    }
}
