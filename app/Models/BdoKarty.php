<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BdoKarty extends Model
{
    protected $table = 'bdo_karty';

    protected $fillable = [
        'kpo_id',
        'corrected_waste_mass',
        'planned_transport_date',
        'planned_transport_time',
        'card_number',
        'card_status',
        'card_status_code_name',
        'waste_code',
        'waste_code_description',
        'calendar_year',
        'sender_name_or_first_name_and_last_name',
        'sender_address',
        'sender_eup_number',
        'sender_eup_name',
        'sender_eup_address',
        'sender_identification_number',
        'sender_nip',
        'carrier_name_or_first_name_and_last_name',
        'carrier_address',
        'carrier_identification_number',
        'carrier_nip',
        'receiver_name_or_first_name_and_last_name',
        'receiver_address',
        'receiver_identification_number',
        'receiver_nip',
        'waste_code_and_description',
        'waste_mass',
        'remarks',
        'vehicle_reg_number',
        'real_transport_date',
        'real_transport_time',
        'receive_confirmation_date',
        'receive_confirmation_time',
        'card_rejection_time',
        'rejected_by_user_first_name_and_last_name',
        'approval_date',
        'approval_time',
        'approved_by_user',
        'transport_confirmation_date',
        'transport_confirmation_time',
        'transport_confirmed_by_user',
        'receive_confirmed_by_user',
        'ewrant',
        'additional_info',
        'kpo_last_modified_at',
    ];

    protected $casts = [
        'card_rejection_time' => 'datetime',
        'kpo_last_modified_at' => 'datetime',
        'planned_transport_date' => 'date',
        'real_transport_date' => 'date',
        'receive_confirmation_date' => 'date',
        'approval_date' => 'date',
        'transport_confirmation_date' => 'date',
        'waste_mass' => 'decimal:4',
        'corrected_waste_mass' => 'decimal:4',
    ];
}
