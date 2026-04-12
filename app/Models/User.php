<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'login',
        'password',
        'module',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Laravel domyślnie używa 'email' – nadpisujemy na 'login'
    public function getAuthIdentifierName(): string
    {
        return 'login';
    }

    const MODULES = [
        'admin'      => 'Administrator',
        'biuro'      => 'Biuro',
        'kierowca'   => 'Kierowca',
        'hakowiec'   => 'Hakowiec',
        'plac'       => 'Plac',
        'handlowiec' => 'Handlowiec',
        'czarnypan'  => 'Czarny Pan',
        'karchem'    => 'Karchem',
    ];

    public function isAdmin(): bool
    {
        return $this->module === 'admin';
    }

    public function moduleName(): string
    {
        return self::MODULES[$this->module] ?? ucfirst($this->module);
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'salesman_id');
    }

}
