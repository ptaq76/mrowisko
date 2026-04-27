<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Container extends Model
{
    protected $fillable = [
        'name',
        'tare_kg',
        'type',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'tare_kg'   => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(ContainerStock::class);
    }

    public function placStock(): HasOne
    {
        return $this->hasOne(ContainerStock::class)->whereNull('client_id');
    }

    public function clientStocks(): HasMany
    {
        return $this->hasMany(ContainerStock::class)->whereNotNull('client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function placQty(): int
    {
        return ContainerStock::placQty($this->id);
    }

    public function clientQty(int $clientId): int
    {
        return ContainerStock::clientQty($this->id, $clientId);
    }

    public function totalQty(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }
}
