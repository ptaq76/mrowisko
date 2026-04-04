<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hauler;

class HaulerController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('short_name')->get();
        $haulerIds = Hauler::pluck('client_id')->toArray();

        return view('biuro.haulers.index', compact('clients', 'haulerIds'));
    }

    public function toggle(Client $client)
    {
        $hauler = Hauler::where('client_id', $client->id)->first();

        if ($hauler) {
            $hauler->delete();
            $isHauler = false;
        } else {
            Hauler::create(['client_id' => $client->id]);
            $isHauler = true;
        }

        return response()->json(['success' => true, 'is_hauler' => $isHauler]);
    }
}
