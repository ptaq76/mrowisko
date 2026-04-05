<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\WasteFraction;
use App\Models\WasteFractionGroup;
use Illuminate\Http\Request;

class WasteFractionController extends Controller
{
    public function index()
    {
        $fractions = WasteFraction::with('group')
            ->orderBy('name')
            ->get();

        $groups = WasteFractionGroup::orderBy('name')->get();

        return view('biuro.fractions.index', compact('fractions', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:waste_fractions,name'],
            'group_id'   => ['nullable', 'exists:waste_fraction_groups,id'],
            'add_belka'  => ['nullable', 'boolean'],
            'show_in_sales'      => ['nullable', 'boolean'],
            'show_in_deliveries' => ['nullable', 'boolean'],
            'show_in_loadings'   => ['nullable', 'boolean'],
            'show_in_production' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Podaj nazwę towaru.',
            'name.unique'   => 'Towar o tej nazwie już istnieje.',
        ]);

        $data = [
            'name'               => trim($request->name),
            'group_id'           => $request->group_id ?: null,
            'show_in_sales'      => (bool) $request->show_in_sales,
            'show_in_deliveries' => (bool) $request->show_in_deliveries,
            'show_in_loadings'   => (bool) $request->show_in_loadings,
            'show_in_production' => (bool) $request->show_in_production,
            'is_active'          => true,
            'allows_belka'       => false,
            'allows_luz'         => true,
        ];

        $fraction = WasteFraction::create($data);
        $belka    = null;

        if ($request->add_belka) {
            $belkaName = trim($request->name) . ' BELKA';
            if (!WasteFraction::where('name', $belkaName)->exists()) {
                $belka = WasteFraction::create(array_merge($data, [
                    'name'         => $belkaName,
                    'allows_belka' => true,
                    'allows_luz'   => false,
                ]));
            }
        }

        return response()->json([
            'success'  => true,
            'fraction' => $fraction,
            'belka'    => $belka,
        ]);
    }

    public function update(Request $request, WasteFraction $fraction)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100', 'unique:waste_fractions,name,' . $fraction->id],
            'group_id' => ['nullable', 'exists:waste_fraction_groups,id'],
        ], [
            'name.required' => 'Podaj nazwę towaru.',
            'name.unique'   => 'Towar o tej nazwie już istnieje.',
        ]);

        $fraction->update([
            'name'     => trim($request->name),
            'group_id' => $request->group_id ?: null,
        ]);

        return response()->json(['success' => true]);
    }

    public function toggle(Request $request, WasteFraction $fraction)
    {
        $request->validate([
            'field' => ['required', 'in:show_in_sales,show_in_deliveries,show_in_loadings,show_in_production,is_active'],
        ]);

        $field = $request->field;
        $fraction->update([$field => !$fraction->$field]);

        return response()->json([
            'success' => true,
            'field'   => $field,
            'value'   => (bool) $fraction->fresh()->$field,
        ]);
    }
}
