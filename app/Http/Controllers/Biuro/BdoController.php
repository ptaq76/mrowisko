<?php

namespace App\Http\Controllers\Biuro;

use App\Http\Controllers\Controller;
use App\Models\BdoKarty;
use App\Models\BdoKartyPrzekazujacy;
use App\Models\KarchemKlienci;
use App\Services\Bdo\BdoLogger;
use App\Services\Bdo\BdoSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BdoController extends Controller
{
    /**
     * Widok kart BDO - pozycja PRZEJMUJĄCEGO
     */
    public function index(Request $request)
    {
        $nipy = KarchemKlienci::pluck('nip')->filter()->toArray();

        $status = $request->get('status', 'wszystkie');
        $nowe = $request->get('nowe', '0') === '1';
        $przekazujacy = $request->get('przekazujacy');

        $transportujacy = $request->has('transportujacy')
            ? $request->get('transportujacy')
            : 'BEZ KARCHEM';

        $kodOdpadu = $request->get('kod_odpadu');

        $query = BdoKarty::select('bdo_karty.*')
            ->whereNotIn('bdo_karty.sender_nip', $nipy)
            ->orderByDesc('bdo_karty.kpo_last_modified_at');

        // Filtr NOWE - poprzedni i bieżący dzień roboczy
        if ($nowe) {
            $now = now();

            if ($now->isMonday() || $now->isWeekend()) {
                $dateFrom = $now->copy()->previous(Carbon::FRIDAY)->startOfDay();
            } else {
                $dateFrom = $now->copy()->subDay()->startOfDay();
            }

            $query->where('bdo_karty.kpo_last_modified_at', '>=', $dateFrom);
        }

        // Filtrowanie według statusu
        if ($status !== 'wszystkie') {
            $statusMap = [
                'wygenerowane' => 'Potwierdzenie wygenerowane',
                'przejecie' => 'Potwierdzenie przejęcia',
                'transport' => 'Potwierdzenie transportu',
                'wycofane' => 'Wycofana',
                'odrzucone' => 'Odrzucona',
                'zatwierdzone' => 'Zatwierdzona',
                'planowane' => 'Planowana',
            ];

            if (isset($statusMap[$status])) {
                $query->where('bdo_karty.card_status', $statusMap[$status]);
            }
        }

        // Filtr przekazujący
        if ($przekazujacy) {
            $query->where('bdo_karty.sender_name_or_first_name_and_last_name', $przekazujacy);
        }

        // Filtr transportujący
        if ($transportujacy && $transportujacy !== '') {
            if ($transportujacy === 'BEZ KARCHEM') {
                $query->where('bdo_karty.carrier_name_or_first_name_and_last_name', '!=', 'PRZEDSIĘBIORSTWO HANDLOWE KARCHEM');
            } else {
                $query->where('bdo_karty.carrier_name_or_first_name_and_last_name', $transportujacy);
            }
        }

        // Filtr kod odpadu
        if ($kodOdpadu) {
            $query->where('bdo_karty.waste_code_and_description', 'LIKE', $kodOdpadu.'%');
        }

        $karty = $query->get();

        // Zlicz karty według statusów
        $statusCounts = BdoKarty::select('bdo_karty.card_status', DB::raw('count(*) as total'))
            ->groupBy('bdo_karty.card_status')
            ->pluck('total', 'card_status')
            ->toArray();

        // Pobierz unikalne wartości dla selectów
        $filtrQuery = BdoKarty::query();

        if ($nowe) {
            $now = now();
            if ($now->isMonday() || $now->isWeekend()) {
                $dateFrom = $now->copy()->previous(Carbon::FRIDAY)->startOfDay();
            } else {
                $dateFrom = $now->copy()->subDay()->startOfDay();
            }
            $filtrQuery->where('kpo_last_modified_at', '>=', $dateFrom);
        }

        if ($status !== 'wszystkie') {
            $statusMap = [
                'wygenerowane' => 'Potwierdzenie wygenerowane',
                'przejecie' => 'Potwierdzenie przejęcia',
                'transport' => 'Potwierdzenie transportu',
                'wycofane' => 'Wycofana',
                'odrzucone' => 'Odrzucona',
                'zatwierdzone' => 'Zatwierdzona',
                'planowane' => 'Planowana',
            ];
            if (isset($statusMap[$status])) {
                $filtrQuery->where('card_status', $statusMap[$status]);
            }
        }

        $przekazujacyList = $filtrQuery->pluck('sender_name_or_first_name_and_last_name')->unique()->filter()->sort()->values();
        $transportujacyList = $filtrQuery->pluck('carrier_name_or_first_name_and_last_name')->unique()->filter()->sort()->values();
        $transportujacyList->prepend('BEZ KARCHEM');

        $kodyOdpadow = $filtrQuery->pluck('waste_code_and_description')->unique()->filter()->map(function ($item) {
            return mb_substr($item, 0, 8, 'UTF-8');
        })->unique()->sort()->values();

        return view('biuro.bdo.karty', compact(
            'karty',
            'status',
            'statusCounts',
            'przekazujacyList',
            'transportujacyList',
            'kodyOdpadow',
            'nowe',
            'przekazujacy',
            'transportujacy',
            'kodOdpadu'
        ));
    }

    /**
     * Widok kart BDO - pozycja PRZEKAZUJĄCEGO
     */
    public function indexPrzekazujacy(Request $request)
    {
        $nipy = KarchemKlienci::pluck('nip')->filter()->toArray();

        $status = $request->get('status', 'wszystkie');
        $nowe = $request->get('nowe', '0') === '1';
        $przejmujacy = $request->get('przejmujacy');
        $transportujacy = $request->get('transportujacy');
        $kodOdpadu = $request->get('kod_odpadu');

        $query = BdoKartyPrzekazujacy::select('bdo_karty_przekazujacy.*')
            ->whereNotIn('bdo_karty_przekazujacy.sender_nip', $nipy)
            ->orderByDesc('bdo_karty_przekazujacy.card_number');

        // Filtr NOWE
        if ($nowe) {
            $now = now();

            if ($now->isMonday() || $now->isWeekend()) {
                $dateFrom = $now->copy()->previous(Carbon::FRIDAY)->startOfDay();
            } else {
                $dateFrom = $now->copy()->subDay()->startOfDay();
            }

            $query->where('bdo_karty_przekazujacy.kpo_last_modified_at', '>=', $dateFrom);
        }

        // Filtrowanie według statusu
        if ($status !== 'wszystkie') {
            $statusMap = [
                'wygenerowane' => 'Potwierdzenie wygenerowane',
                'przejecie' => 'Potwierdzenie przejęcia',
                'transport' => 'Potwierdzenie transportu',
                'wycofane' => 'Wycofana',
                'odrzucone' => 'Odrzucona',
                'zatwierdzone' => 'Zatwierdzona',
                'planowane' => 'Planowana',
            ];

            if (isset($statusMap[$status])) {
                $query->where('bdo_karty_przekazujacy.card_status', $statusMap[$status]);
            }
        }

        // Filtr przejmujący
        if ($przejmujacy) {
            $query->where('bdo_karty_przekazujacy.receiver_name_or_first_name_and_last_name', $przejmujacy);
        }

        // Filtr transportujący
        if ($transportujacy && $transportujacy !== '') {
            if ($transportujacy === 'BEZ KARCHEM') {
                $query->where('bdo_karty_przekazujacy.carrier_name_or_first_name_and_last_name', '!=', 'PRZEDSIĘBIORSTWO HANDLOWE KARCHEM');
            } else {
                $query->where('bdo_karty_przekazujacy.carrier_name_or_first_name_and_last_name', $transportujacy);
            }
        }

        // Filtr kod odpadu
        if ($kodOdpadu) {
            $query->where('bdo_karty_przekazujacy.waste_code_and_description', 'LIKE', $kodOdpadu.'%');
        }

        $karty = $query->get();

        // Zlicz karty według statusów
        $statusCounts = BdoKartyPrzekazujacy::select('bdo_karty_przekazujacy.card_status', DB::raw('count(*) as total'))
            ->groupBy('bdo_karty_przekazujacy.card_status')
            ->pluck('total', 'card_status')
            ->toArray();

        // Pobierz unikalne wartości dla selectów
        $filtrQuery = BdoKartyPrzekazujacy::select('bdo_karty_przekazujacy.*');

        if ($nowe) {
            $now = now();
            if ($now->isMonday()) {
                $dateFrom = $now->copy()->previous(Carbon::FRIDAY)->startOfDay();
            } else {
                $dateFrom = $now->copy()->subHours(24);
            }
            $filtrQuery->where('bdo_karty_przekazujacy.kpo_last_modified_at', '>=', $dateFrom);
        }

        if ($status !== 'wszystkie') {
            $statusMap = [
                'wygenerowane' => 'Potwierdzenie wygenerowane',
                'przejecie' => 'Potwierdzenie przejęcia',
                'transport' => 'Potwierdzenie transportu',
                'wycofane' => 'Wycofana',
                'odrzucone' => 'Odrzucona',
                'zatwierdzone' => 'Zatwierdzona',
                'planowane' => 'Planowana',
            ];
            if (isset($statusMap[$status])) {
                $filtrQuery->where('bdo_karty_przekazujacy.card_status', $statusMap[$status]);
            }
        }

        $przejmujacyList = $filtrQuery->pluck('receiver_name_or_first_name_and_last_name')->unique()->filter()->sort()->values();
        $transportujacyList = $filtrQuery->pluck('carrier_name_or_first_name_and_last_name')->unique()->filter()->sort()->values();
        $transportujacyList->prepend('BEZ KARCHEM');

        $kodyOdpadow = $filtrQuery->pluck('waste_code_and_description')->unique()->filter()->map(function ($item) {
            return mb_substr($item, 0, 8, 'UTF-8');
        })->unique()->sort()->values();

        return view('biuro.bdo.kartyPrzekazujacy', compact(
            'karty',
            'status',
            'statusCounts',
            'przejmujacyList',
            'transportujacyList',
            'kodyOdpadow',
            'nowe',
            'przejmujacy',
            'transportujacy',
            'kodOdpadu'
        ));
    }

    /**
     * Synchronizacja kart - pozycja przejmującego
     */
    public function sync(BdoSyncService $bdoSyncService)
    {
        try {
            $result = $bdoSyncService->fetchAndSync();

            return response()->json([
                'success' => $result['status'] === 'SUCCESS',
                'total' => $result['total'] ?? 0,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'skipped' => $result['skipped'] ?? 0,
                'errors' => $result['errors'] ?? 0,
                'message' => $result['message'] ?? '',
            ]);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd synchronizacji BDO', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Synchronizacja kart - pozycja przekazującego
     */
    public function syncPrzekazujacy(BdoSyncService $bdoSyncService)
    {
        try {
            $result = $bdoSyncService->fetchAndSyncPrzekazujacy();

            return response()->json([
                'success' => $result['status'] === 'SUCCESS',
                'total' => $result['total'] ?? 0,
                'created' => $result['created'] ?? 0,
                'updated' => $result['updated'] ?? 0,
                'skipped' => $result['skipped'] ?? 0,
                'errors' => $result['errors'] ?? 0,
                'message' => $result['message'] ?? '',
            ]);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd synchronizacji BDO Przekazujący', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Potwierdza masę na karcie KPO
     */
    public function potwierdzKarte(Request $request)
    {
        try {
            $validated = $request->validate([
                'karta_id' => 'required|integer',
                'waste_mass' => 'required|numeric',
                'kpo_id' => 'required|string',
            ]);

            $kartaId = $validated['karta_id'];
            $wasteMass = number_format((float) $validated['waste_mass'], 3, '.', '');
            $kpoId = $validated['kpo_id'];

            $karta = DB::table('bdo_karty')->where('id', $kartaId)->first();
            if (! $karta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nie znaleziono karty w bazie danych',
                ], 404);
            }

            $calendar_year = $karta->calendar_year;

            $bdoSync = new BdoSyncService;
            $result = $bdoSync->confirmWasteCard($kpoId, $wasteMass);

            if ($result) {
                $updateResult = $bdoSync->fetchAndUpdateSingleCard($kpoId, $calendar_year);

                if (! $updateResult) {
                    BdoLogger::warning('Nie udało się zaktualizować karty z BDO', [
                        'kpo_id' => $kpoId,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Karta została potwierdzona',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Błąd potwierdzania karty',
            ], 500);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd potwierdzania karty', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Odrzuca kartę KPO
     */
    public function odrzucKarte(Request $request)
    {
        try {
            $validated = $request->validate([
                'karta_id' => 'required|integer',
                'waste_mass' => 'required|numeric',
                'kpo_id' => 'required|string',
            ]);

            $kartaId = $validated['karta_id'];
            $wasteMass = number_format((float) $validated['waste_mass'], 3, '.', '');
            $kpoId = $validated['kpo_id'];

            $karta = DB::table('bdo_karty')->where('id', $kartaId)->first();
            if (! $karta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nie znaleziono karty w bazie danych',
                ], 404);
            }

            $calendar_year = $karta->calendar_year;

            $bdoSync = new BdoSyncService;
            $result = $bdoSync->rejectWasteCard($kpoId, $wasteMass);

            if ($result) {
                $updateResult = $bdoSync->fetchAndUpdateSingleCard($kpoId, $calendar_year);

                if (! $updateResult) {
                    BdoLogger::warning('Nie udało się zaktualizować karty z BDO', [
                        'kpo_id' => $kpoId,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Karta została odrzucona',
                    'waste_mass' => number_format($wasteMass, 3, ',', ' '),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Błąd odrzucania karty',
            ], 500);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd odrzucania karty', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aktualizuje pojedynczą kartę z API BDO
     */
    public function aktualizujJednaKarte(Request $request)
    {
        try {
            $validated = $request->validate([
                'karta_id' => 'required|integer|exists:bdo_karty,id',
                'kpo_id' => 'required|string',
                'calendar_year' => 'required|integer',
            ]);

            $bdoSync = new BdoSyncService;
            $result = $bdoSync->fetchAndUpdateSingleCard(
                $validated['kpo_id'],
                $validated['calendar_year']
            );

            if (! $result) {
                BdoLogger::warning('Nie udało się zaktualizować karty z BDO', [
                    'kpo_id' => $validated['kpo_id'],
                    'calendar_year' => $validated['calendar_year'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nie udało się zaktualizować karty z API BDO',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Karta została zaktualizowana',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Błąd walidacji: '.implode(', ', array_map(fn ($errors) => implode(', ', $errors), $e->errors())),
            ], 422);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd aktualizacji karty', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił nieoczekiwany błąd podczas aktualizacji',
            ], 500);
        }
    }

    /**
     * Potwierdza rozpoczęcie transportu
     */
    public function potwierdzRozpoczecie(Request $request)
    {
        try {
            $validated = $request->validate([
                'kpo_id' => 'required|string',
                'nr_transportu' => 'required|string',
                'data_start' => 'required|date',
                'czas_start' => 'required|string',
            ]);

            $kpoId = $validated['kpo_id'];
            $vehicleRegNumber = $validated['nr_transportu'];
            $dataStart = $validated['data_start'];
            $czasStart = $validated['czas_start'];

            $realDateTime = new \DateTime("$dataStart $czasStart");
            $realDateTimeIso = $realDateTime->format(\DateTime::ATOM);

            $bdoSync = new BdoSyncService;
            $result = $bdoSync->confirmTransportStart(
                $kpoId,
                $vehicleRegNumber,
                $realDateTimeIso,
                $czasStart
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transport został potwierdzony',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Błąd podczas potwierdzania transportu w BDO',
            ], 500);

        } catch (\Throwable $e) {
            BdoLogger::error('Błąd potwierdzania rozpoczęcia transportu', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
