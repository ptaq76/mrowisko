<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'nip',
        'bdo',
        'country',
        'type',
        'street',
        'postal_code',
        'city',
        'phone',
        'email',
        'notes',
        'salesman_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const TYPES = [
        'pickup' => 'Dostawca',
        'sale'   => 'Odbiorca',
        'both'   => 'Dostawca i odbiorca',
    ];

    const COUNTRIES = [
        'PL' => 'Polska',
        'DE' => 'Niemcy',
    ];

    // Relacje
    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function addresses()
    {
        return $this->hasMany(ClientAddress::class)->orderBy('city');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPickup($query)
    {
        return $query->whereIn('type', ['pickup', 'both']);
    }

    public function scopeForSale($query)
    {
        return $query->whereIn('type', ['sale', 'both']);
    }

    // Helpers
    public function typeName(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function typeColor(): string
    {
        return match($this->type) {
            'pickup' => 'bg-primary',
            'sale'   => 'bg-success',
            'both'   => 'bg-warning text-dark',
            default  => 'bg-secondary',
        };
    }

        public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pickupRequests()
{
    return $this->hasMany(\App\Models\PickupRequest::class);
}
}
