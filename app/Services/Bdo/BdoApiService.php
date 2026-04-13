<?php

namespace App\Services\Bdo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BdoApiService
{
    protected string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Pobiera jedną stronę kart KPO (receiver/search) - pozycja PRZEJMUJĄCEGO
     */
    public function fetchWasteCardsPage(int $year, int $page, int $pageSize = 50): array
    {
        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/receiver/search';

        $payload = [
            "PaginationParameters" => [
                "Order" => [
                    "IsAscending" => true,
                ],
                "Page" => [
                    "Index" => $page,
                    "Size" => $pageSize
                ]
            ],
            "Year" => $year,
            "SearchInCarriers" => true,
            "SearchInSenders" => true,
            "Name" => "",
            "Locality" => "",
            "Street" => "",
            "Nip" => "",
            "IdentificationNumber" => "",
            "WasteCodeAndDescription" => "",
            "CardNumber" => "",
            "CardStatusCodeNames" => "",
            "TransportTime" => "",
            "ReceiveConfirmationTime" => "",
            "SenderFirstNameAndLastName" => "",
            "ReceiverFirstAndLastName" => "",
            "VehicleRegNumber" => "",
            "TransportDateRange" => false,
            "TransportDateFrom" => "",
            "TransportDateTo" => "",
            "ReceiveConfirmationDateRange" => false,
            "ReceiveConfirmationDateFrom" => "",
            "ReceiveConfirmationDateTo" => ""
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->timeout(60)
                ->post($url, $payload);

            if ($response->failed()) {
                BdoLogger::error("BDOApiService fetchWasteCardsPage failed", [
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            return $response->json('items') ?? [];
        } catch (\Exception $e) {
            BdoLogger::error("BDOApiService fetchWasteCardsPage exception", [
                'page' => $page,
                'exception' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Pobiera jedną stronę kart KPO (sender/search) - pozycja PRZEKAZUJĄCEGO
     */
    public function fetchWasteCardsPagePrzekazujacy(int $year, int $page, int $pageSize = 50, ?string $transportDateFrom = null): array
    {
        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/sender/search';

        $payload = [
            "PaginationParameters" => [
                "Order" => [
                    "IsAscending" => true,
                    "OrderColumn" => "cardNumber"
                ],
                "Page" => [
                    "Index" => $page,
                    "Size" => $pageSize
                ]
            ],
            "Year" => $year,
            "SearchInCarriers" => true,
            "SearchInReceivers" => true,
            "Name" => "",
            "Locality" => "",
            "Street" => "",
            "Nip" => "",
            "IdentificationNumber" => "",
            "WasteCodeAndDescription" => "",
            "CardNumber" => "",
            "CardStatusCodeNames" => "",
            "TransportTime" => "",
            "ReceiveConfirmationTime" => "",
            "SenderFirstNameAndLastName" => "",
            "ReceiverFirstAndLastName" => "",
            "VehicleRegNumber" => "",
            "TransportDateRange" => !empty($transportDateFrom),
            "TransportDateFrom" => $transportDateFrom ?? "",
            "TransportDateTo" => "",
            "ReceiveConfirmationDateRange" => false,
            "ReceiveConfirmationDateFrom" => "",
            "ReceiveConfirmationDateTo" => ""
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->timeout(60)
                ->post($url, $payload);

            if ($response->failed()) {
                BdoLogger::error("BDOApiService fetchWasteCardsPagePrzekazujacy failed", [
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            return $response->json('items') ?? [];
        } catch (\Exception $e) {
            BdoLogger::error("BDOApiService fetchWasteCardsPagePrzekazujacy exception", [
                'page' => $page,
                'exception' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Pobiera pojedynczą kartę po numerze karty (przejmujący)
     */
    public function fetchSingleCardByNumber(string $cardNumber, int $year): ?array
    {
        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/receiver/search';

        $payload = [
            "PaginationParameters" => [
                "Order" => [
                    "IsAscending" => true,
                    "OrderColumn" => "cardNumber"
                ],
                "Page" => [
                    "Index" => 1,
                    "Size" => 1
                ]
            ],
            "Year" => $year,
            "SearchInCarriers" => true,
            "SearchInSenders" => true,
            "CardNumber" => $cardNumber,
            "Name" => "",
            "Locality" => "",
            "Street" => "",
            "Nip" => "",
            "IdentificationNumber" => "",
            "WasteCodeAndDescription" => "",
            "CardStatusCodeNames" => "",
            "TransportTime" => "",
            "ReceiveConfirmationTime" => "",
            "SenderFirstNameAndLastName" => "",
            "ReceiverFirstAndLastName" => "",
            "VehicleRegNumber" => "",
            "TransportDateRange" => false,
            "TransportDateFrom" => "",
            "TransportDateTo" => "",
            "ReceiveConfirmationDateRange" => false,
            "ReceiveConfirmationDateFrom" => "",
            "ReceiveConfirmationDateTo" => ""
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->timeout(30)
                ->post($url, $payload);

            if ($response->failed()) {
                BdoLogger::error("BDOApiService fetchSingleCardByNumber failed", [
                    'cardNumber' => $cardNumber,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $items = $response->json('items') ?? [];
            return !empty($items) ? $items[0] : null;

        } catch (\Exception $e) {
            BdoLogger::error("BDOApiService fetchSingleCardByNumber exception", [
                'cardNumber' => $cardNumber,
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Pobiera pojedynczą kartę po numerze karty (przekazujący)
     */
    public function fetchSingleCardByNumberPrzekazujacy(string $cardNumber, int $year): ?array
    {
        $url = 'https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/sender/search';

        $payload = [
            "PaginationParameters" => [
                "Order" => [
                    "IsAscending" => true,
                    "OrderColumn" => "cardNumber"
                ],
                "Page" => [
                    "Index" => 1,
                    "Size" => 1
                ]
            ],
            "Year" => $year,
            "SearchInCarriers" => true,
            "SearchInSenders" => true,
            "CardNumber" => $cardNumber,
            "Name" => "",
            "Locality" => "",
            "Street" => "",
            "Nip" => "",
            "IdentificationNumber" => "",
            "WasteCodeAndDescription" => "",
            "CardStatusCodeNames" => "",
            "TransportTime" => "",
            "ReceiveConfirmationTime" => "",
            "SenderFirstNameAndLastName" => "",
            "ReceiverFirstAndLastName" => "",
            "VehicleRegNumber" => "",
            "TransportDateRange" => false,
            "TransportDateFrom" => "",
            "TransportDateTo" => "",
            "ReceiveConfirmationDateRange" => false,
            "ReceiveConfirmationDateFrom" => "",
            "ReceiveConfirmationDateTo" => ""
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->timeout(30)
                ->post($url, $payload);

            if ($response->failed()) {
                BdoLogger::error("BDOApiService fetchSingleCardByNumberPrzekazujacy failed", [
                    'cardNumber' => $cardNumber,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $items = $response->json('items') ?? [];
            return !empty($items) ? $items[0] : null;

        } catch (\Exception $e) {
            BdoLogger::error("BDOApiService fetchSingleCardByNumberPrzekazujacy exception", [
                'cardNumber' => $cardNumber,
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Pobiera szczegóły karty po kpoId
     */
    public function fetchCardDetails(string $kpoId): ?array
    {
        $url = "https://rejestr-bdo.mos.gov.pl/api/WasteRegister/WasteTransferCard/v1/Kpo/printingpage?KpoId={$kpoId}";

        try {
            $response = Http::withToken($this->accessToken)
                ->acceptJson()
                ->timeout(30)
                ->get($url);

            if ($response->failed()) {
                BdoLogger::error("BDOApiService fetchCardDetails failed", [
                    'kpoId' => $kpoId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $cardData = $response->json();

            if (empty($cardData)) {
                return null;
            }

            return $cardData;

        } catch (\Exception $e) {
            BdoLogger::error("BDOApiService fetchCardDetails exception", [
                'kpoId' => $kpoId,
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }
}
