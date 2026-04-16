<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id', 'name', 'full_name', 'firma', 'color',
        'phone', 'tractor_id', 'trailer_id', 'avatar', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tractor()
    {
        return $this->belongsTo(Vehicle::class, 'tractor_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Vehicle::class, 'trailer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function todayOrders()
    {
        return $this->hasMany(Order::class)->whereDate('planned_date', now()->toDateString());
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('drivers/'.$this->avatar);
        }

        return '';
    }

    public function initials(): string
    {
        $parts = explode(' ', $this->full_name);

        return strtoupper(substr($parts[0], 0, 1).(isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
}
