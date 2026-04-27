<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\LoadingItem;
use App\Models\Order;
use App\Models\WarehouseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShortcutController extends Controller
{
    /**
     * POST /biuro/shortcuts/recykler
     * Tworzy stub dostawę dla klienta Recykler (id=113)
     * fraction_id=20, bales=0, weight z formularza (tony → kg)
     */
    public function recykler(Request $request)
    {
        $data = $request->validate([
            'tons' => 'required|numeric|min:0.001|max:100',
        ]);

        $weightKg = round($data['tons'] * 1000, 2);
        $userId   = auth()->user()->id;

        DB::transaction(function () use ($weightKg, $userId) {
            $order = Order::create([
                'type'         => 'pickup',
                'client_id'    => 113,
                'planned_date' => now()->toDateString(),
                'status'       => 'closed',
                'weight_netto' => $weightKg / 1000,
                'notes'        => 'Biuro – skrót Recykler',
            ]);

            LoadingItem::create([
                'order_id'    => $order->id,
                'fraction_id' => 20,
                'bales'       => 0,
                'weight_kg'   => $weightKg,
                'notes'       => 'Biuro',
                'operator_id' => $userId,
            ]);

            WarehouseItem::create([
                'date'            => now()->toDateString(),
                'fraction_id'     => 20,
                'bales'           => 0,
                'weight_kg'       => $weightKg,
                'origin'          => 'delivery',
                'origin_order_id' => $order->id,
                'operator_id'     => $userId,
                'notes'           => 'Biuro – skrót Recykler',
            ]);
        });

        return response()->json(['success' => true]);
    }
}
