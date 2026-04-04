<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteFraction extends Model
{
    protected $fillable = [
        'name',
        'group_id',
        'allows_luz',
        'allows_belka',
        'sells_as_luz',
        'show_in_sales',
        'show_in_deliveries',
        'show_in_loadings',
        'show_in_production',
        'client_id',
        'is_active',
    ];

    protected $casts = [
        'allows_luz'         => 'boolean',
        'allows_belka'       => 'boolean',
        'sells_as_luz'       => 'boolean',
        'show_in_sales'      => 'boolean',
        'show_in_deliveries' => 'boolean',
        'show_in_loadings'   => 'boolean',
        'show_in_production' => 'boolean',
        'is_active'          => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(WasteFractionGroup::class, 'group_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Określa formę na podstawie nazwy (LUZ / BELKA w nazwie)
    public function getFormAttribute(): string
    {
        if (str_contains(strtoupper($this->name), 'BELKA')) return 'belka';
        if (str_contains(strtoupper($this->name), 'LUZ'))   return 'luz';
        return 'inne';
    }

    // Scopy
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDeliveries($query)
    {
        return $query->where('show_in_deliveries', true)->where('is_active', true);
    }

    public function scopeForLoadings($query)
    {
        return $query->where('show_in_loadings', true)->where('is_active', true);
    }

    public function scopeForProduction($query)
    {
        return $query->where('show_in_production', true)->where('is_active', true);
    }
}
