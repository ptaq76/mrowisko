<?php

namespace App\Services\Bdo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BdoSyncService
{
    private $pageSize = 50;

    /**
     * Synchronizacja kart Przejmujący
     */
    public function fetchAndSync(): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $syncStopped = false;
        $failureDetails = null;

        $auth = new BdoAuthService();
        $token = $auth->generateToken();

        if (!$token) {
            throw new \Exception("Brak tokenu BDO");
        }

        BdoLogger::info("BDO Sync started");

        $api = new BdoApiService($token);
        $currentYear = (int)date('Y');
        $startYear = 2024;
        $allCardsToProcess = [];

        // Iteruj od roku bieżącego wstecz do 2024
        for ($year = $currentYear; $year >= $startYear; $year--) {
            BdoLogger::info("BDO: Rozpoczynam pobieranie kart dla roku", ['year' => $year]);

            $secondLastModified = DB::table('bdo_karty')
                ->select('kpo_last_modified_at')
                ->whereNotNull('kpo_last_modified_at')
                ->whereYear('kpo_last_modified_at', $year)
                ->orderBy('kpo_last_modified_at', 'desc')
                ->skip(1)
                ->first();

            $cutoffDate = $secondLastModified
                ? Carbon::parse($secondLastModified->kpo_last_modified_at)
                : null;

            BdoLogger::info("BDO: Cutoff date dla roku {$year}", [
                'cutoff_date' => $cutoffDate ? $cutoffDate->toDateTimeString() : 'NULL (będzie pobrane wszystko)',
                'is_first_sync_for_year' => $cutoffDate === null
            ]);

            $page = 1;
            $cardsForYear = [];

            while (true) {
                $cards = $api->fetchWasteCardsPage($year, $page, $this->pageSize);

                if (!is_array($cards) || empty($cards)) {
                    break;
                }

                $cardsForYear = array_merge($cardsForYear, $cards);
                $page++;

                if ($page > 100) {
                    BdoLogger::warning("BDO: Przerwano po 100 stronach dla roku {$year}");
                    break;
                }
            }

            BdoLogger::info("BDO: Pobrano kart dla roku {$year} z API", ['count' => count($cardsForYear)]);

            if ($cutoffDate) {
                $originalCount = count($cardsForYear);

                $cardsForYear = array_filter($cardsForYear, function ($card) use ($cutoffDate) {
                    $kpoLastModified = $card['kpoLastModifiedAt'] ?? null;

                    if (!$kpoLastModified) {
                        return true;
                    }

                    $cardDate = Carbon::parse($kpoLastModified);
                    return $cardDate->greaterThan($cutoffDate);
                });

                $cardsForYear = array_values($cardsForYear);

                BdoLogger::info("BDO: Filtrowanie zakończone dla roku {$year}", [
                    'original_count' => $originalCount,
                    'after_filter_count' => count($cardsForYear),
                    'filtered_out' => $originalCount - count($cardsForYear),
                    'cutoff_date' => $cutoffDate->toDateTimeString()
                ]);
            }

            $allCardsToProcess = array_merge($allCardsToProcess, $cardsForYear);
        }

        BdoLogger::info("BDO: Pobrano łącznie kart do przetworzenia (po filtrowaniu)", ['count' => count($allCardsToProcess)]);

        // Sortuj wszystkie karty chronologicznie
        usort($allCardsToProcess, function ($a, $b) {
            $dateA = $a['kpoLastModifiedAt'] ?? '1970-01-01T00:00:00';
            $dateB = $b['kpoLastModifiedAt'] ?? '1970-01-01T00:00:00';
            return strcmp($dateA, $dateB);
        });

        $mapper = new BdoMapperService();

        $chunkSize = 50;
        $delayBetweenCards = 200000;
        $delayBetweenChunks = 2000000;

        $chunks = array_chunk($allCardsToProcess, $chunkSize);

        foreach ($chunks as $chunkIndex => $chunk) {
            if ($syncStopped) {
                break;
            }

            $chunkStartTime = microtime(true);

            foreach ($chunk as $item) {
                if ($syncStopped) {
                    break;
                }

                $kpoId = $item['kpoId'] ?? null;

                if (!$kpoId) {
                    BdoLogger::warning("BDO: Brak kpoId w item z API", ['item' => $item]);
                    $errors++;
                    continue;
                }

                $kpoLastModified = $item['kpoLastModifiedAt'] ?? null;

                usleep($delayBetweenCards);

                $detailData = retry(3, function () use ($api, $kpoId) {
                    return $api->fetchCardDetails($kpoId);
                }, 500);

                if (!$detailData) {
                    BdoLogger::error("BDO: KRYTYCZNY BŁĄD - nie udało się pobrać detali karty. SYNC ZATRZYMANY.", [
                        'kpo_id' => $kpoId,
                        'card_number' => $item['cardNumber'] ?? 'BRAK',
                        'kpo_last_modified_at' => $kpoLastModified,
                        'chunk_number' => $chunkIndex + 1,
                        'cards_processed_before_failure' => $created + $updated + $skipped,
                        'overall_progress' => ($created + $updated + $skipped + $errors) . '/' . count($allCardsToProcess),
                    ]);

                    $syncStopped = true;
                    $failureDetails = [
                        'total' => count($allCardsToProcess),
                        'created' => $created,
                        'updated' => $updated,
                        'skipped' => $skipped,
                        'errors' => $errors + 1,
                        'difference' => count($allCardsToProcess) - ($created + $updated + $skipped + $errors + 1),
                        'stopped_at_card' => $kpoId,
                        'stopped_at_card_number' => $item['cardNumber'] ?? 'BRAK',
                        'status' => 'FAILED',
                        'message' => 'Sync zatrzymany z powodu błędu pobierania detali karty.'
                    ];
                    break;
                }

                $mapped = $mapper->mapToBdoKartyDetale($item, $detailData);

                if (empty($mapped['kpo_id'])) {
                    BdoLogger::warning("BDO: Pominięto kartę - brak kpo_id po mapowaniu", [
                        'original_kpoId' => $kpoId,
                    ]);
                    $errors++;
                    continue;
                }

                $existing = DB::table('bdo_karty')
                    ->where('kpo_id', $kpoId)
                    ->first();

                try {
                    if ($existing) {
                        $existingDate = Carbon::parse($existing->kpo_last_modified_at);
                        $newDate = Carbon::parse($kpoLastModified);

                        if ($kpoLastModified && $newDate->greaterThan($existingDate)) {
                            unset($mapped['created_at']);

                            DB::table('bdo_karty')
                                ->where('id', $existing->id)
                                ->update($mapped);
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        DB::table('bdo_karty')->insert($mapped);
                        $created++;
                    }
                } catch (\Exception $e) {
                    Log::error("BDO: Błąd zapisu karty do bazy", [
                        'kpo_id' => $kpoId,
                        'card_number' => $mapped['card_number'] ?? 'BRAK',
                        'exception' => $e->getMessage(),
                    ]);
                    $errors++;
                }
            }

            if ($syncStopped) {
                break;
            }

            if ($chunkIndex < count($chunks) - 1) {
                usleep($delayBetweenChunks);
            }
        }

        if ($syncStopped && $failureDetails) {
            BdoLogger::error("BDO Sync FAILED - zwracam szczegóły błędu", $failureDetails);
            return $failureDetails;
        }

        $summary = [
            'total' => count($allCardsToProcess),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'difference' => count($allCardsToProcess) - ($created + $updated + $skipped + $errors),
            'status' => 'SUCCESS',
            'message' => 'Wszystkie karty przetworzone pomyślnie'
        ];

        BdoLogger::info("BDO Sync zakończona - podsumowanie", $summary);

        return $summary;
    }

    /**
     * Synchronizacja kart Przekazujący
     */
    public function fetchAndSyncPrzekazujacy(): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $syncStopped = false;
        $failureDetails = null;

        $auth = new BdoAuthService();
        $token = $auth->generateToken();

        if (!$token) {
            throw new \Exception("Brak tokenu BDO");
        }

        $secondLastModified = DB::table('bdo_karty_przekazujacy')
            ->select('kpo_last_modified_at')
            ->whereNotNull('kpo_last_modified_at')
            ->orderBy('kpo_last_modified_at', 'desc')
            ->skip(1)
            ->first();

        $cutoffDate = $secondLastModified
            ? Carbon::parse($secondLastModified->kpo_last_modified_at)
            : null;

        BdoLogger::info("BDO Przekazujący Sync started", [
            'is_first_sync' => $cutoffDate === null,
            'kpo_last_modified_at_cutoff' => $cutoffDate ? $cutoffDate->toDateTimeString() : 'NULL (będzie pobrane wszystko)'
        ]);

        $api = new BdoApiService($token);
        $year = date('Y');
        $page = 1;
        $allCards = [];

        while (true) {
            $cards = $api->fetchWasteCardsPagePrzekazujacy($year, $page, $this->pageSize);

            if (!is_array($cards) || empty($cards)) {
                break;
            }

            $allCards = array_merge($allCards, $cards);
            $page++;

            if ($page > 100) {
                BdoLogger::warning("BDO Przekazujący: Przerwano po 100 stronach");
                break;
            }
        }

        BdoLogger::info("BDO Przekazujący: Pobrano kart z API", ['count' => count($allCards)]);

        usort($allCards, function ($a, $b) {
            $dateA = $a['kpoLastModifiedAt'] ?? '1970-01-01T00:00:00';
            $dateB = $b['kpoLastModifiedAt'] ?? '1970-01-01T00:00:00';
            return strcmp($dateA, $dateB);
        });

        if ($cutoffDate) {
            $originalCount = count($allCards);

            $allCards = array_filter($allCards, function ($card) use ($cutoffDate) {
                $kpoLastModified = $card['kpoLastModifiedAt'] ?? null;

                if (!$kpoLastModified) {
                    return true;
                }

                $cardDate = Carbon::parse($kpoLastModified);
                return $cardDate->greaterThan($cutoffDate);
            });

            $allCards = array_values($allCards);

            BdoLogger::info("BDO Przekazujący: Filtrowanie zakończone", [
                'original_count' => $originalCount,
                'after_filter_count' => count($allCards),
                'filtered_out' => $originalCount - count($allCards),
                'cutoff_date' => $cutoffDate->toDateTimeString()
            ]);
        }

        $mapper = new BdoMapperService();

        $chunkSize = 50;
        $delayBetweenCards = 200000;
        $delayBetweenChunks = 2000000;

        $chunks = array_chunk($allCards, $chunkSize);

        foreach ($chunks as $chunkIndex => $chunk) {
            if ($syncStopped) {
                break;
            }

            foreach ($chunk as $item) {
                if ($syncStopped) {
                    break;
                }

                $kpoId = $item['kpoId'] ?? null;

                if (!$kpoId) {
                    BdoLogger::warning("BDO Przekazujący: Brak kpoId w item z API", ['item' => $item]);
                    $errors++;
                    continue;
                }

                $kpoLastModified = $item['kpoLastModifiedAt'] ?? null;

                usleep($delayBetweenCards);

                $detailData = retry(3, function () use ($api, $kpoId) {
                    return $api->fetchCardDetails($kpoId);
                }, 300);

                if (!$detailData) {
                    BdoLogger::error("BDO Przekazujący: KRYTYCZNY BŁĄD - nie udało się pobrać detali karty. SYNC ZATRZYMANY.", [
                        'kpo_id' => $kpoId,
                        'card_number' => $item['cardNumber'] ?? 'BRAK',
                    ]);

                    $syncStopped = true;
                    $failureDetails = [
                        'total' => count($allCards),
                        'created' => $created,
                        'updated' => $updated,
                        'skipped' => $skipped,
                        'errors' => $errors + 1,
                        'status' => 'FAILED',
                        'message' => 'Sync zatrzymany z powodu błędu pobierania detali karty.'
                    ];
                    break;
                }

                $mapped = $mapper->mapToBdoKartyDetale($item, $detailData);

                if (empty($mapped['kpo_id'])) {
                    $errors++;
                    continue;
                }

                $existing = DB::table('bdo_karty_przekazujacy')
                    ->where('kpo_id', $kpoId)
                    ->first();

                try {
                    if ($existing) {
                        $existingDate = Carbon::parse($existing->kpo_last_modified_at);
                        $newDate = Carbon::parse($kpoLastModified);

                        if ($kpoLastModified && $newDate->greaterThan($existingDate)) {
                            unset($mapped['created_at']);

                            DB::table('bdo_karty_przekazujacy')
                                ->where('id', $existing->id)
                                ->update($mapped);
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        DB::table('bdo_karty_przekazujacy')->insert($mapped);
                        $created++;
                    }
                } catch (\Exception $e) {
                    Log::error("BDO Przekazujący: Błąd zapisu karty do bazy", [
                        'kpo_id' => $kpoId,
                        'exception' => $e->getMessage(),
                    ]);
                    $errors++;
                }
            }

            if ($syncStopped) {
                break;
            }

            if ($chunkIndex < count($chunks) - 1) {
                usleep($delayBetweenChunks);
            }
        }

        if ($syncStopped && $failureDetails) {
            BdoLogger::error("BDO Przekazujący Sync FAILED", $failureDetails);
            return $failureDetails;
        }

        $summary = [
            'total' => count($allCards),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'status' => 'SUCCESS',
            'message' => 'Wszystkie karty przetworzone pomyślnie'
        ];

        BdoLogger::info("BDO Przekazujący Sync zakończona - podsumowanie", $summary);

        return $summary;
    }

    /**
     * Aktualizuje pojedynczą kartę z API BDO
     */
    public function fetchAndUpdateSingleCard(string $kpoId, int $year): bool
    {
        try {
            $auth = new BdoAuthService();
            $token = $auth->generateToken();

            if (!$token) {
                BdoLogger::error("fetchAndUpdateSingleCard: Brak tokenu BDO", ['kpo_id' => $kpoId]);
                return false;
            }

            $url = "https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/printingpage";

            $detailData = retry(3, function () use ($url, $token, $kpoId) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(30)
                    ->get($url, ['KpoId' => $kpoId]);

                if ($response->failed()) {
                    BdoLogger::warning("fetchAndUpdateSingleCard: Błąd pobierania detali", [
                        'kpo_id' => $kpoId,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return null;
                }

                return $response->json();
            }, 500);

            if (!$detailData) {
                BdoLogger::error("fetchAndUpdateSingleCard: Nie udało się pobrać detali karty", ['kpo_id' => $kpoId]);
                return false;
            }

            $mapper = new BdoMapperService();

            $listItem = [
                'cardNumber' => $detailData['cardNumber'] ?? null,
                'calendarYear' => $year,
                'kpoId' => $kpoId,
                'kpoLastModifiedAt' => $detailData['kpoLastModifiedAt'] ?? null
            ];

            $mapped = $mapper->mapToBdoKartyDetale($listItem, $detailData);

            if (empty($mapped['kpo_id'])) {
                BdoLogger::error("fetchAndUpdateSingleCard: Brak kpo_id po mapowaniu", ['kpo_id' => $kpoId]);
                return false;
            }

            $existing = DB::table('bdo_karty')
                ->where('kpo_id', $kpoId)
                ->first();

            if (!$existing) {
                BdoLogger::error("fetchAndUpdateSingleCard: Nie znaleziono karty w bazie", ['kpo_id' => $kpoId]);
                return false;
            }

            unset($mapped['created_at']);
            unset($mapped['kpo_last_modified_at']);

            DB::table('bdo_karty')
                ->where('id', $existing->id)
                ->update($mapped);

            BdoLogger::info("fetchAndUpdateSingleCard: Zaktualizowano kartę", [
                'kpo_id' => $kpoId,
                'id' => $existing->id
            ]);

            return true;

        } catch (\Throwable $e) {
            BdoLogger::error("fetchAndUpdateSingleCard: Nieoczekiwany błąd", [
                'kpo_id' => $kpoId,
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Potwierdza masę na karcie KPO
     */
    public function confirmWasteCard(string $kpoId, float $wasteMass): bool
    {
        $auth = new BdoAuthService();
        $accessToken = $auth->generateToken();

        if (!$accessToken) {
            BdoLogger::error("Brak tokenu przy potwierdzaniu karty KPO", [
                'kpo_id' => $kpoId,
                'masa' => $wasteMass
            ]);
            return false;
        }

        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/assign/receiveconfirmation';

        $data = [
            'CorrectedWasteMass' => $wasteMass,
            'KpoId' => $kpoId,
            'Remarks' => '',
        ];

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->put($url, $data);

            if ($response->successful()) {
                BdoLogger::info("Potwierdzono kartę KPO", [
                    'kpo_id' => $kpoId,
                    'masa' => $wasteMass,
                    'response' => $response->json()
                ]);
                return true;
            }

            BdoLogger::error("Błąd potwierdzania karty KPO", [
                'kpo_id' => $kpoId,
                'masa' => $wasteMass,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Throwable $e) {
            BdoLogger::error("Wyjątek przy potwierdzaniu karty KPO", [
                'kpo_id' => $kpoId,
                'msg' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Odrzuca kartę KPO
     */
    public function rejectWasteCard(string $kpoId, float $wasteMass): bool
    {
        $auth = new BdoAuthService();
        $accessToken = $auth->generateToken();

        if (!$accessToken) {
            BdoLogger::error("Brak tokenu przy odrzucaniu karty KPO", [
                'kpo_id' => $kpoId,
                'masa' => $wasteMass
            ]);
            return false;
        }

        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/reject';

        $remarks = "Poprawna masa to: {$wasteMass}";

        $data = [
            'KpoId' => $kpoId,
            'Remarks' => $remarks,
        ];

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->put($url, $data);

            if ($response->successful()) {
                BdoLogger::info("Odrzucono kartę KPO", [
                    'kpo_id' => $kpoId,
                    'masa' => $wasteMass,
                    'remarks' => $remarks,
                ]);
                return true;
            }

            BdoLogger::error("Błąd odrzucania karty KPO", [
                'kpo_id' => $kpoId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Throwable $e) {
            BdoLogger::error("Wyjątek przy odrzucaniu karty KPO", [
                'kpo_id' => $kpoId,
                'msg' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Potwierdza rozpoczęcie transportu
     */
    public function confirmTransportStart(
        string $kpoId,
        string $vehicleRegNumber,
        string $realTransportDate,
        string $realTransportTime
    ): bool {
        try {
            $auth = new BdoAuthService();
            $token = $auth->generateToken();

            $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/carrier/update/approved/generateconfirmation';

            $dateTime = new \DateTime($realTransportDate);
            $dateTime->setTimezone(new \DateTimeZone('UTC'));
            $realTransportDateFormatted = $dateTime->format('Y-m-d\TH:i:s.v\Z');

            $payload = [
                "KpoId" => $kpoId,
                "VehicleRegNumber" => $vehicleRegNumber,
                "RealTransportTime" => $realTransportTime,
                "RealTransportDate" => $realTransportDateFormatted
            ];

            BdoLogger::info("Wysyłanie potwierdzenia transportu do BDO", [
                'url' => $url,
                'method' => 'PUT',
                'kpo_id' => $kpoId,
                'payload' => $payload
            ]);

            $response = Http::withToken($token)
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(30)
                ->put($url, $payload);

            if ($response->successful()) {
                BdoLogger::info("Potwierdzenie transportu zakończone sukcesem", [
                    'kpo_id' => $kpoId,
                    'response' => $response->json()
                ]);
                return true;
            }

            BdoLogger::error("Błąd BDO przy potwierdzaniu transportu", [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            return false;

        } catch (\Throwable $e) {
            Log::error("Wyjątek przy confirmTransportStart", [
                'message' => $e->getMessage(),
                'kpo_id' => $kpoId
            ]);
            return false;
        }
    }
}
