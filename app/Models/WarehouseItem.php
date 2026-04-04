<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseItem extends Model
{
    protected $table = 'warehouse_items';

    protected $fillable = [
        'date', 'fraction_id', 'weight_kg', 'bales',
        'origin', 'origin_order_id', 'operator_id', 'notes',
    ];

    protected $casts = [
        'date'      => 'date',
        'weight_kg' => 'decimal:2',
    ];

    const ORIGINS = [
        'production' => 'Produkcja',
        'loading'    => 'Załadunek',
        'delivery'   => 'Dostawa',
        'inventory'  => 'Inwentaryzacja',
    ];

    public function fraction()
    {
        return $this->belongsTo(WasteFraction::class, 'fraction_id');
    }

    public function originOrder()
    {
        return $this->belongsTo(Order::class, 'origin_order_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Aktualny stan magazynu per frakcja (belki i waga)
    public static function stockSummary()
    {
        return self::selectRaw('fraction_id, SUM(bales) as total_bales, SUM(weight_kg) as total_weight')
            ->groupBy('fraction_id')
            ->having('total_bales', '>', 0)
            ->with('fraction')
            ->get();
    }

    // Stan dla konkretnej frakcji
    public static function stockForFraction(int $fractionId): array
    {
        $row = self::selectRaw('SUM(bales) as total_bales, SUM(weight_kg) as total_weight')
            ->where('fraction_id', $fractionId)
            ->first();

        return [
            'bales'  => (int) ($row->total_bales ?? 0),
            'weight' => (float) ($row->total_weight ?? 0),
        ];
    }

    // Średnia waga belki dla frakcji (na podstawie wpisów produkcji)
    public static function avgBaleWeight(int $fractionId): float
    {
        $row = self::selectRaw('SUM(bales) as total_bales, SUM(weight_kg) as total_weight')
            ->where('fraction_id', $fractionId)
            ->where('origin', 'production')  // tylko z produkcji, żeby nie zaburzać średniej załadunkami
            ->first();

        $bales  = (int)   ($row->total_bales  ?? 0);
        $weight = (float) ($row->total_weight ?? 0);

        if ($bales === 0 || $weight <= 0) return 0;
        return round(abs($weight) / abs($bales), 2);
    }
}
