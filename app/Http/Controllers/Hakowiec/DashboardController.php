<?php

namespace App\Http\Controllers\Hakowiec;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Zadanie;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('data')
            ? Carbon::parse($request->input('data'))->startOfDay()
            : Carbon::today();

        $driver = Driver::where('user_id', auth()->user()->id)->first();

        $zadania = collect();
        if ($driver) {
            $zadania = Zadanie::with('creator')
                ->forDriver($driver->id)
                ->onDate($date)
                ->orderBy('status')
                ->orderBy('id')
                ->get();
        }

        return view('hakowiec.dashboard', compact('driver', 'zadania', 'date'));
    }
}
