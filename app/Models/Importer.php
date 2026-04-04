<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importer extends Model
{
    protected $fillable = ['name', 'country', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    const COUNTRIES = ['PL' => 'Polska', 'DE' => 'Niemcy'];

    public function lieferscheins()
    {
        return $this->hasMany(Lieferschein::class);
    }
}
