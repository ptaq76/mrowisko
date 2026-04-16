<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'module',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [];

    // Automatyczne hashowanie hasła przy zapisie (kompatybilne z L9/L10)
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Dostępne moduły
    const MODULES = [
        'admin' => 'Administrator',
        'biuro' => 'Biuro',
        'kierowca' => 'Kierowca',
        'hakowiec' => 'Hakowiec',
        'plac' => 'Plac',
        'handlowiec' => 'Handlowiec',
    ];

    public function isAdmin(): bool
    {
        return $this->module === 'admin';
    }

    public function moduleName(): string
    {
        return self::MODULES[$this->module] ?? ucfirst($this->module);
    }

    // Relacje
    public function clients()
    {
        return $this->hasMany(Client::class, 'salesman_id');
    }
}
