<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MigrationReportController extends Controller
{
    public function index(Request $request)
    {
        $oldDb = DB::connection('mrowisko');

        // Pobierz słowniki nowej bazy do walidacji FK (jako sety dla szybkiego in_array)
        $clientIds        = DB::table('clients')->pluck('id')->flip();
        $driverIds        = DB::table('drivers')->pluck('id')->flip();
        $vehicleIds       = DB::table('vehicles')->pluck('id')->flip();
        $lieferscheinIds  = DB::table('lieferscheins')->pluck('id')->flip();
        $fractionIds      = DB::table('waste_fractions')->pluck('id')->flip();

        // Pobierz wszystkie planowanie ze starej bazy
        $planowanie = $oldDb->table('planowanie')->orderBy('data', 'desc')->get();

        // Prefetch powiązań (mniej zapytań niż w pętli)
        $planNaPlacByPlan = $oldDb->table('plan_na_plac')
            ->whereIn('planowanie_id', $planowanie->pluck('id'))
            ->get()
            ->keyBy('planowanie_id');

        $wazeniaByPlan = $oldDb->table('wazenia')
            ->whereIn('planowanie_id', $planowanie->pluck('id'))
            ->get()
            ->keyBy('planowanie_id');

        $planNaPlacIds = $planNaPlacByPlan->pluck('id');

        $dostawyByPlanNaPlac = $oldDb->table('dostawy')
            ->whereIn('plan_na_plac_id', $planNaPlacIds)
            ->get()
            ->keyBy('plan_na_plac_id');

        $zaladunkiByPlanNaPlac = $oldDb->table('zaladunki')
            ->whereIn('plan_na_plac_id', $planNaPlacIds)
            ->get()
            ->keyBy('plan_na_plac_id');

        $dostawyTowary = $oldDb->table('dostawy_towary')
            ->whereIn('dostawy_id', $dostawyByPlanNaPlac->pluck('id'))
            ->get()
            ->groupBy('dostawy_id');

        $zaladunkiTowary = $oldDb->table('zaladunki_towary')
            ->whereIn('zaladunki_id', $zaladunkiByPlanNaPlac->pluck('id'))
            ->get()
            ->groupBy('zaladunki_id');

        // Próg "świeżości" — zsynchronizowany z $keepRecentDays w MigrateOrdersSeeder
        $keepRecentDays = 10;
        $recentThreshold = now()->subDays($keepRecentDays)->toDateString();

        // Klasyfikacja każdego rekordu planowanie
        $rows = [];
        $stats = [
            'total' => $planowanie->count(),
            'ok' => 0,                      // pełen komplet (closed)
            'kept_no_wazenia' => 0,         // wykonane, brak wazenia (delivered)
            'kept_recent_no_exec' => 0,     // świeże, niewykonane (planned)
            'skip_no_plan_na_plac' => 0,
            'skip_no_towary' => 0,
            'fk_problems' => 0,
        ];
        $fkCounts = [
            'client_id' => 0,
            'driver_id' => 0,
            'lieferschein_id' => 0,
            'tractor_id' => 0,
            'trailer_id' => 0,
            'start_client_id' => 0,
            'fraction_id' => 0,
        ];

        foreach ($planowanie as $p) {
            $row = [
                'planowanie_id' => $p->id,
                'data' => $p->data,
                'godzina' => $p->godzina,
                'rodzaj' => $p->rodzaj,
                'type' => $p->rodzaj === 'O' ? 'pickup' : 'sale',
                'kontrahent_cel' => $p->kontrahent_cel,
                'start' => $p->start,
                'kierowca_id' => $p->kierowca_id,
                'ciagnik_id' => $p->ciagnik_id,
                'naczepa_id' => $p->naczepa_id,
                'ls_id' => $p->ls_id,
                'towary_note' => $p->towary,
                'status' => null,
                'reason' => null,
                'fk_issues' => [],
                'towary_count' => 0,
                'towary_invalid_fraction_ids' => [],
            ];

            // Sprawdź łańcuch zależności
            $planNaPlac = $planNaPlacByPlan->get($p->id);
            $wazenie = $wazeniaByPlan->get($p->id);

            // Towary z dostawy/zaladunki
            $towary = collect();
            if ($planNaPlac) {
                if ($p->rodzaj === 'O') {
                    $dostawa = $dostawyByPlanNaPlac->get($planNaPlac->id);
                    $towary = $dostawa ? ($dostawyTowary->get($dostawa->id) ?? collect()) : collect();
                } else {
                    $zaladunek = $zaladunkiByPlanNaPlac->get($planNaPlac->id);
                    $towary = $zaladunek ? ($zaladunkiTowary->get($zaladunek->id) ?? collect()) : collect();
                }
            }

            $wasExecuted = ! $towary->isEmpty();
            $isRecent = $p->data && $p->data >= $recentThreshold;

            // Pomiń stare i niewykonane (ta sama logika co seeder)
            if (! $wasExecuted && ! $isRecent) {
                $row['status'] = 'skipped';
                if (! $planNaPlac) {
                    $row['reason'] = 'Brak plan_na_plac (stare, niewykonane)';
                    $stats['skip_no_plan_na_plac']++;
                } else {
                    $row['reason'] = 'Brak towarów w '.($p->rodzaj === 'O' ? 'dostawy_towary' : 'zaladunki_towary').' (stare, niewykonane)';
                    $stats['skip_no_towary']++;
                }
                $rows[] = $row;
                continue;
            }

            // Klasyfikacja zachowanych
            if ($wasExecuted && $wazenie) {
                $row['migration_status'] = 'closed';
            } elseif ($wasExecuted) {
                $row['migration_status'] = 'delivered';
                $stats['kept_no_wazenia']++;
            } else {
                $row['migration_status'] = 'planned';
                $stats['kept_recent_no_exec']++;
            }

            // Walidacja FK na nowej bazie
            $row['towary_count'] = $towary->count();

            if ($p->kontrahent_cel && ! $clientIds->has($p->kontrahent_cel)) {
                $row['fk_issues'][] = 'client_id';
                $fkCounts['client_id']++;
            }
            if ($p->start && ! $clientIds->has($p->start)) {
                $row['fk_issues'][] = 'start_client_id';
                $fkCounts['start_client_id']++;
            }
            if ($p->kierowca_id && ! $driverIds->has($p->kierowca_id)) {
                $row['fk_issues'][] = 'driver_id';
                $fkCounts['driver_id']++;
            }
            if ($p->ciagnik_id && ! $vehicleIds->has($p->ciagnik_id)) {
                $row['fk_issues'][] = 'tractor_id';
                $fkCounts['tractor_id']++;
            }
            if ($p->naczepa_id && ! $vehicleIds->has($p->naczepa_id)) {
                $row['fk_issues'][] = 'trailer_id';
                $fkCounts['trailer_id']++;
            }
            if ($p->ls_id && ! $lieferscheinIds->has($p->ls_id)) {
                $row['fk_issues'][] = 'lieferschein_id';
                $fkCounts['lieferschein_id']++;
            }

            $invalidFractions = [];
            foreach ($towary as $t) {
                if ($t->towar_id && ! $fractionIds->has($t->towar_id)) {
                    $invalidFractions[] = $t->towar_id;
                }
            }
            if (! empty($invalidFractions)) {
                $row['fk_issues'][] = 'fraction_id';
                $row['towary_invalid_fraction_ids'] = array_values(array_unique($invalidFractions));
                $fkCounts['fraction_id']++;
            }

            if (! empty($row['fk_issues'])) {
                $row['status'] = 'fk_problem';
                $row['reason'] = "Migrowane jako '{$row['migration_status']}' z problemami FK: ".implode(', ', $row['fk_issues']);
                $stats['fk_problems']++;
            } else {
                $row['status'] = 'ok';
                $row['reason'] = "OK (status='{$row['migration_status']}')";
                if ($row['migration_status'] === 'closed') {
                    $stats['ok']++;
                }
            }

            $rows[] = $row;
        }

        // Filtr z URL
        $filter = $request->input('filter', 'all');
        if ($filter !== 'all') {
            $rows = array_values(array_filter($rows, function ($r) use ($filter) {
                return match ($filter) {
                    'ok'                  => $r['status'] === 'ok',
                    'closed'              => ($r['migration_status'] ?? null) === 'closed',
                    'delivered'           => ($r['migration_status'] ?? null) === 'delivered',
                    'planned'             => ($r['migration_status'] ?? null) === 'planned',
                    'skipped'             => $r['status'] === 'skipped',
                    'fk'                  => $r['status'] === 'fk_problem',
                    'no_plan_na_plac'     => str_contains($r['reason'] ?? '', 'Brak plan_na_plac'),
                    'no_towary'           => str_contains($r['reason'] ?? '', 'Brak towarów'),
                    'fk_client'           => in_array('client_id', $r['fk_issues']),
                    'fk_start'            => in_array('start_client_id', $r['fk_issues']),
                    'fk_driver'           => in_array('driver_id', $r['fk_issues']),
                    'fk_tractor'          => in_array('tractor_id', $r['fk_issues']),
                    'fk_trailer'          => in_array('trailer_id', $r['fk_issues']),
                    'fk_ls'               => in_array('lieferschein_id', $r['fk_issues']),
                    'fk_fraction'         => in_array('fraction_id', $r['fk_issues']),
                    default               => true,
                };
            }));
        }

        return view('biuro.migration_report', [
            'rows'     => $rows,
            'stats'    => $stats,
            'fkCounts' => $fkCounts,
            'filter'   => $filter,
        ]);
    }
}
