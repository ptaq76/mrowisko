<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteFractionGroup extends Model
{
    protected $fillable = ['name'];

    public function fractions()
    {
        return $this->hasMany(WasteFraction::class, 'group_id');
    }
}
