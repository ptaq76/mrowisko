<?php

namespace App\Services\Bdo;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BdoAuthService
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.bdo.client_id');
        $this->clientSecret = config('services.bdo.client_secret');
    }

    /**
     * Pobiera listę EUP i zwraca pierwszy EupId
     */
    public function getEupId(): ?string
    {
        try {
            $response = Http::post('https://rejestr-bdo.mos.gov.pl/api/WasteRegister/v1/Auth/getEupList', [
                'ClientId' => $this->clientId,
                'ClientSecret' => $this->clientSecret,
                'PaginationParameters' => [
                    'Order' => ['IsAscending' => true],
                    'Page' => ['Index' => 7, 'Size' => 0],
                ],
            ]);

            if ($response->failed()) {
                Log::error('BDO getEupId failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $items = $response->json('items') ?? [];
            return $items[0]['eupId'] ?? null;

        } catch (\Exception $e) {
            Log::error('BDO getEupId exception', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generuje AccessToken z EUP (z cache)
     */
    public function generateToken(?string $eupId = null): ?string
    {
        if (!$eupId) {
            $eupId = $this->getEupId();
        }
        if (!$eupId) {
            Log::error('BDO generateToken failed: brak EupId');
            return null;
        }

        $cacheKey = "bdo_token_{$eupId}";
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::post('https://rejestr-bdo.mos.gov.pl/api/WasteRegister/v1/Auth/generateEupAccessToken', [
                'ClientId' => $this->clientId,
                'ClientSecret' => $this->clientSecret,
                'EupId' => $eupId,
            ]);

            if ($response->failed()) {
                Log::error('BDO generateToken failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $token = $response->json('AccessToken') ?? $response->json('accessToken') ?? null;

            if ($token) {
                // cache na 55 minut
                Cache::put($cacheKey, $token, 3300);
                return $token;
            }

            Log::error('BDO generateToken: brak AccessToken w odpowiedzi', ['body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('BDO generateToken exception', ['msg' => $e->getMessage()]);
            return null;
        }
    }
}
