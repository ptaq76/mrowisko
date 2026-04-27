<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ContainerStock extends Model
{
    protected $table = 'container_stock';

    protected $fillable = [
        'container_id',
        'client_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopePlac($q)
    {
        return $q->whereNull('client_id');
    }

    public function scopeAtClient($q, int $clientId)
    {
        return $q->where('client_id', $clientId);
    }

    public static function placQty(int $containerId): int
    {
        return (int) self::query()
            ->where('container_id', $containerId)
            ->whereNull('client_id')
            ->value('quantity');
    }

    public static function clientQty(int $containerId, int $clientId): int
    {
        return (int) self::query()
            ->where('container_id', $containerId)
            ->where('client_id', $clientId)
            ->value('quantity');
    }

    /**
     * Przenosi 1 szt. z placu do klienta. Atomowo. Rzuca wyjątek przy niewystarczającym stocku.
     */
    public static function moveToClient(int $containerId, int $clientId, int $qty = 1): void
    {
        DB::transaction(function () use ($containerId, $clientId, $qty) {
            $placRow = self::lockForUpdate()
                ->where('container_id', $containerId)
                ->whereNull('client_id')
                ->first();

            if (! $placRow || $placRow->quantity < $qty) {
                throw new \RuntimeException('Brak wystarczającej ilości na placu (kontener #'.$containerId.').');
            }

            $placRow->decrement('quantity', $qty);

            $clientRow = self::lockForUpdate()
                ->where('container_id', $containerId)
                ->where('client_id', $clientId)
                ->first();

            if ($clientRow) {
                $clientRow->increment('quantity', $qty);
            } else {
                self::create([
                    'container_id' => $containerId,
                    'client_id'    => $clientId,
                    'quantity'     => $qty,
                ]);
            }
        });
    }

    /**
     * Przenosi 1 szt. od klienta na plac. Atomowo. Rzuca wyjątek przy niewystarczającym stocku.
     */
    public static function moveToPlac(int $containerId, int $clientId, int $qty = 1): void
    {
        DB::transaction(function () use ($containerId, $clientId, $qty) {
            $clientRow = self::lockForUpdate()
                ->where('container_id', $containerId)
                ->where('client_id', $clientId)
                ->first();

            if (! $clientRow || $clientRow->quantity < $qty) {
                throw new \RuntimeException('Brak wystarczającej ilości u klienta (kontener #'.$containerId.', klient #'.$clientId.').');
            }

            $clientRow->decrement('quantity', $qty);

            // jeśli stock klienta spadł do 0 — usuwamy wiersz
            if ($clientRow->fresh()->quantity <= 0) {
                $clientRow->delete();
            }

            $placRow = self::lockForUpdate()
                ->where('container_id', $containerId)
                ->whereNull('client_id')
                ->first();

            if ($placRow) {
                $placRow->increment('quantity', $qty);
            } else {
                self::create([
                    'container_id' => $containerId,
                    'client_id'    => null,
                    'quantity'     => $qty,
                ]);
            }
        });
    }

    /**
     * Bezpośredni adjust (do panelu w biurze).
     */
    public static function adjust(int $containerId, ?int $clientId, int $delta): void
    {
        DB::transaction(function () use ($containerId, $clientId, $delta) {
            $row = self::lockForUpdate()
                ->where('container_id', $containerId)
                ->when($clientId === null, fn ($q) => $q->whereNull('client_id'))
                ->when($clientId !== null, fn ($q) => $q->where('client_id', $clientId))
                ->first();

            if ($row) {
                $newQty = $row->quantity + $delta;
                if ($newQty < 0) {
                    throw new \RuntimeException('Stan nie może być ujemny.');
                }
                if ($newQty === 0 && $clientId !== null) {
                    $row->delete();
                } else {
                    $row->update(['quantity' => $newQty]);
                }
            } elseif ($delta > 0) {
                self::create([
                    'container_id' => $containerId,
                    'client_id'    => $clientId,
                    'quantity'     => $delta,
                ]);
            } elseif ($delta < 0) {
                throw new \RuntimeException('Brak rekordu — nie można zmniejszyć.');
            }
        });
    }
}
