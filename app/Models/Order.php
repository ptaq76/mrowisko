<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'type',
        'client_id',
        'driver_id',
        'start_client_id',
        'tractor_id',
        'trailer_id',
        'lieferschein_id',
        'planned_date',
        'plac_date',
        'planned_time',
        'fractions_note',
        'notes',
        'status',
        'weight_brutto',
        'weight_netto',
        'weight_receiver',
        'weight_original',
        'confirmed_at_client',
        'weight_accepted_by',
        'weight_accepted_at',
        'is_archived',
        'is_unplanned',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'plac_date' => 'date',
        'confirmed_at_client' => 'datetime',
        'weight_accepted_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_archived' => 'boolean',
        'is_unplanned' => 'boolean',
        'weight_brutto' => 'decimal:3',
        'weight_netto' => 'decimal:3',
        'weight_receiver' => 'decimal:3',
        'weight_original' => 'decimal:3',
    ];

    const STATUSES_PICKUP = [
        'planned' => 'Zaplanowane',
        'in_progress' => 'W trakcie realizacji',
        'weighed' => 'Zważone',
        'classified' => 'Sklasyfikowane',
        'closed' => 'Zamknięte',
    ];

    const STATUSES_SALE = [
        'planned' => 'Zaplanowane',
        'loading' => 'W trakcie załadunku',
        'weighed' => 'Zważone',
        'closed' => 'Zamknięte',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function startClient()
    {
        return $this->belongsTo(Client::class, 'start_client_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function tractor()
    {
        return $this->belongsTo(Vehicle::class, 'tractor_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Vehicle::class, 'trailer_id');
    }

    public function lieferschein()
    {
        return $this->belongsTo(Lieferschein::class);
    }

    public function warehouseDeliveryItems()
    {
        return $this->hasMany(WarehouseItem::class, 'origin_order_id')
            ->where('origin', 'delivery');
    }

    public function wysylkaTransport()
    {
        return $this->hasOne(WysylkaTransport::class);
    }

    public function wysylkaCena()
    {
        return $this->hasOne(WysylkaCena::class);
    }

    public function warehouseLoadingItems()
    {
        return $this->hasMany(WarehouseItem::class, 'origin_order_id')
            ->where('origin', 'loading');
    }

    public function loadingItems()
    {
        return $this->hasMany(LoadingItem::class);
    }

    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderContainers()
    {
        return $this->hasMany(OrderContainer::class);
    }

    public function statusName(): string
    {
        $statuses = $this->type === 'pickup'
            ? self::STATUSES_PICKUP
            : self::STATUSES_SALE;

        return $statuses[$this->status] ?? $this->status;
    }

    public function packaging() {
    return $this->hasMany(OrderPackaging::class);
}
}
