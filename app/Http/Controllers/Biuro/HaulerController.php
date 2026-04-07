<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hauler;
use Illuminate\Http\JsonResponse;

class HaulerController extends Controller
{
    /**
     * Wyświetla listę wszystkich klientów wraz z informacją, 
     * którzy z nich są oznaczeni jako woźacy.
     */
    public function index()
    {
        // Pobieramy wszystkich klientów posortowanych alfabetycznie
        $clients = Client::orderBy('short_name')->get();
        
        // Pobieramy tylko ID klientów, którzy są w tabeli haulers
        $haulerIds = Hauler::pluck('client_id')->toArray();

        return view('biuro.haulers.index', compact('clients', 'haulerIds'));
    }

    /**
     * Przełącza status woźaka dla danego klienta.
     */
    public function toggle(Client $client): JsonResponse
    {
        try {
            $hauler = Hauler::where('client_id', $client->id)->first();

            if ($hauler) {
                // Jeśli istnieje – usuwamy go z listy woźaków
                $hauler->delete();
                $isHauler = false;
            } else {
                // Jeśli nie istnieje – tworzymy nowy wpis
                // Korzystamy tylko z client_id, nazwa zostanie pobrana relacją
                Hauler::create([
                    'client_id' => $client->id,
                    'sort_order' => 0 // Domyślna wartość, skoro masz ją w migracji
                ]);
                $isHauler = true;
            }

            return response()->json([
                'success' => true, 
                'is_hauler' => $isHauler
            ]);

        } catch (\Exception $e) {
            // W razie błędu zwracamy informację dla konsoli JS
            return response()->json([
                'success' => false,
                'error' => 'Błąd serwera: ' . $e->getMessage()
            ], 500);
        }
    }
}