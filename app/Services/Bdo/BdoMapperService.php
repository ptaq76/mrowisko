<?php

namespace App\Services\Bdo;

use Illuminate\Support\Facades\Log;

class BdoMapperService
{
    /**
     * Mapuje dane z API BDO do formatu tabeli bdo_karty / bdo_karty_przekazujacy
     */
    public function mapToBdoKarta(array $listItem, array $detail): array
    {
        return [
            'kpo_id' => $listItem['kpoId'] ?? null,
            'card_number' => $detail['cardNumber'] ?? ($listItem['cardNumber'] ?? null),
            'card_status' => $detail['cardStatus'] ?? ($listItem['cardStatus'] ?? null),
            'card_status_code_name' => $listItem['cardStatusCodeName'] ?? null,
            'waste_code' => $listItem['wasteCode'] ?? null,
            'waste_code_description' => $listItem['wasteCodeDescription'] ?? null,
            'vehicle_reg_number' => $detail['vehicleRegNumber'] ?? ($listItem['vehicleRegNumber'] ?? null),
            'planned_transport_time' => $this->toMysqlDate($listItem['plannedTransportTime'] ?? null),
            'real_transport_time' => $this->toMysqlDate($listItem['realTransportTime'] ?? null),
            'receive_confirmation_time' => $this->toMysqlDate($listItem['receiveConfirmationTime'] ?? null),
            'card_rejection_time' => $this->toMysqlDate($listItem['cardRejectionTime'] ?? null),
            'kpo_last_modified_at' => $this->toMysqlDate($listItem['kpoLastModifiedAt'] ?? ($listItem['kpoLastModifiedAtUtc'] ?? null)),
            'rejected_by_user_first_name_and_last_name' => $listItem['rejectedByUserFirstNameAndLastName'] ?? null,
            'calendar_year' => $detail['calendarYear'] ?? null,
            'sender_name_or_first_name_and_last_name' => $detail['senderNameOrFirstNameAndLastName'] ?? null,
            'sender_address' => $detail['senderAddress'] ?? null,
            'sender_eup_number' => $detail['senderEupNumber'] ?? null,
            'sender_eup_name' => $detail['senderEupName'] ?? null,
            'sender_eup_address' => $detail['senderEupAddress'] ?? null,
            'sender_identification_number' => $detail['senderIdentificationNumber'] ?? null,
            'sender_nip' => $detail['senderNip'] ?? null,
            'carrier_name_or_first_name_and_last_name' => $detail['carrierNameOrFirstNameAndLastName'] ?? null,
            'carrier_address' => $detail['carrierAddress'] ?? null,
            'carrier_identification_number' => $detail['carrierIdentificationNumber'] ?? null,
            'carrier_nip' => $detail['carrierNip'] ?? null,
            'receiver_name_or_first_name_and_last_name' => $detail['receiverNameOrFirstNameAndLastName'] ?? null,
            'receiver_address' => $detail['receiverAddress'] ?? null,
            'receiver_identification_number' => $detail['receiverIdentificationNumber'] ?? null,
            'receiver_nip' => $detail['receiverNip'] ?? null,
            'waste_code_and_description' => $detail['wasteCodeAndDescription'] ?? null,
            'waste_mass' => $detail['wasteMass'] ?? null,
            'corrected_waste_mass' => $detail['correctedWasteMass'] ?? null,
            'remarks' => $detail['remarks'] ?? null,
            'planned_transport_date' => $this->toMysqlDate($detail['plannedTransportDate'] ?? null),
            'real_transport_date' => $this->toMysqlDate($detail['realTransportDate'] ?? null),
            'receive_confirmation_date' => $this->toMysqlDate($detail['receiveConfirmationDate'] ?? null),
            'approval_date' => $this->toMysqlDate($detail['approvalDate'] ?? null),
            'approval_time' => $this->toMysqlDate($detail['approvalTime'] ?? null),
            'approved_by_user' => $detail['approvedByUser'] ?? null,
            'transport_confirmation_date' => $this->toMysqlDate($detail['transportConfirmationDate'] ?? null),
            'transport_confirmation_time' => $this->toMysqlDate($detail['transportConfirmationTime'] ?? null),
            'transport_confirmed_by_user' => $detail['transportConfirmedByUser'] ?? null,
            'receive_confirmed_by_user' => $detail['receiveConfirmedByUser'] ?? null,
            'additional_info' => $detail['additionalInfo'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Konwertuje datę z formatu API do MySQL
     */
    private function toMysqlDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        try {
            return date('Y-m-d H:i:s', strtotime($value));
        } catch (\Throwable $e) {
            Log::warning('BDO: invalid datetime format', ['value' => $value, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
