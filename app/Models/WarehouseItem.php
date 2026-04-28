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
        'date' => 'date',
        'weight_kg' => 'decimal:2',
    ];

    const ORIGINS = [
        'production' => 'Produkcja',
        'loading' => 'Załadunek',
        'delivery' => 'Dostawa',
        'inventory' => 'Inwentaryzacja',
    ];

    // Krótkie kody do wyświetlenia w pill-ach (historia magazynu)
    const ORIGIN_SHORT = [
        'production' => 'PRO',
        'loading'    => 'ZAL',
        'delivery'   => 'DOS',
        'inventory'  => 'INW',
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

    /**
     * Mapa stanu magazynu per fraction_id z semantyką snapshot dla inwentaryzacji:
     * jeśli była inwentaryzacja, stan = wartości z inwentaryzacji + sumy wpisów po niej
     * (chronologicznie po dacie, w razie remisu po id).
     * Bez inwentaryzacji — sumujemy wszystko.
     *
     * Zwraca: Collection keyed by fraction_id z obiektami {total_bales, total_weight}
     */
    public static function computeStockMap(): \Illuminate\Support\Collection
    {
        $items = self::select('id', 'fraction_id', 'bales', 'weight_kg', 'date', 'origin')
            ->orderBy('fraction_id')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $result = collect();

        foreach ($items->groupBy('fraction_id') as $fractionId => $fractionItems) {
            // Indeks ostatniej inwentaryzacji (kolekcja jest już chronologiczna)
            $lastInvIdx = null;
            foreach ($fractionItems as $idx => $item) {
                if ($item->origin === 'inventory') {
                    $lastInvIdx = $idx;
                }
            }

            $relevant = $lastInvIdx !== null
                ? $fractionItems->slice($lastInvIdx)
                : $fractionItems;

            $result->put($fractionId, (object) [
                'fraction_id'  => $fractionId,
                'total_bales'  => (int) $relevant->sum('bales'),
                'total_weight' => round($relevant->sum(fn ($i) => (float) $i->weight_kg), 2),
            ]);
        }

        return $result;
    }

    // Aktualny stan magazynu per frakcja (z eagerload frakcji, tylko dodatnie belki)
    public static function stockSummary()
    {
        $stockMap = self::computeStockMap();
        $fractionIds = $stockMap->keys();
        $fractions = WasteFraction::whereIn('id', $fractionIds)->get()->keyBy('id');

        return $stockMap
            ->filter(fn ($s) => $s->total_bales > 0)
            ->map(function ($s) use ($fractions) {
                $s->fraction = $fractions->get($s->fraction_id);

                return $s;
            })
            ->values();
    }

    // Stan dla konkretnej frakcji
    public static function stockForFraction(int $fractionId): array
    {
        $row = self::computeStockMap()->get($fractionId);

        return [
            'bales' => (int) ($row->total_bales ?? 0),
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

        $bales = (int) ($row->total_bales ?? 0);
        $weight = (float) ($row->total_weight ?? 0);

        if ($bales === 0 || $weight <= 0) {
            return 0;
        }

        return round(abs($weight) / abs($bales), 2);
    }
}
