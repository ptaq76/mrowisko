<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Przewoznik extends Model
{
    protected $table    = 'przewoznicy';
    protected $fillable = ['nazwa', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function koszty()
    {
        return $this->hasMany(KosztTransportu::class);
    }
}
