<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    protected $fillable = [
        'client_id', 'salesman_id', 'order_id',
        'requested_date', 'notes', 'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(PickupRequestItem::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'nowe'            => 'Nowe',
            'przyjete'        => 'Przyjęte',
            'zrealizowane'    => 'Zrealizowane',
            'anulowane'       => 'Anulowane',
            'odrzucone_biuro' => 'Odrzucone przez biuro',
            default           => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'nowe'            => '#f39c12',
            'przyjete'        => '#2980b9',
            'zrealizowane'    => '#27ae60',
            'anulowane'       => '#e74c3c',
            'odrzucone_biuro' => '#8e44ad',
            default           => '#aaa',
        };
    }
}