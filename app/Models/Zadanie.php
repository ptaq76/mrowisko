<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Zadanie extends Model
{
    protected $table = 'zadania';

    protected $fillable = [
        'tresc', 'data', 'target', 'driver_id', 'batch_id',
        'status', 'completed_at', 'completed_by_user_id', 'created_by_user_id',
    ];

    protected $casts = [
        'data' => 'date',
        'completed_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function scopeOnDate(Builder $q, $date): Builder
    {
        return $q->whereDate('data', $date);
    }

    public function scopeForDriver(Builder $q, int $driverId): Builder
    {
        return $q->where('driver_id', $driverId);
    }

    public function scopeForPlac(Builder $q): Builder
    {
        return $q->whereNull('driver_id');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function batchHasDone(): bool
    {
        if (! $this->batch_id) {
            return $this->isDone();
        }

        return self::where('batch_id', $this->batch_id)->where('status', 'done')->exists();
    }
}
