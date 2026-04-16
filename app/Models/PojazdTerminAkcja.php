<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PojazdTerminAkcja extends Model
{
    protected $table = 'pojazdy_terminy_akcje';

    protected $fillable = ['pojazd_id', 'action_type', 'completed_date', 'deadline_date', 'notes'];

    protected $casts = [
        'completed_date' => 'date',
        'deadline_date' => 'date',
    ];

    public function pojazd()
    {
        return $this->belongsTo(PojazdTermin::class, 'pojazd_id');
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (! $this->deadline_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->deadline_date->startOfDay(), false);
    }

    public function getStatusColorAttribute(): string
    {
        $days = $this->days_until_deadline;
        if ($days === null) {
            return 'secondary';
        }
        if ($days < 0) {
            return 'danger';
        }
        if ($days <= 7) {
            return 'danger';
        }
        if ($days <= 30) {
            return 'warning';
        }

        return 'success';
    }
}
