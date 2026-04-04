<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Annex7Shipment extends Model
{
    protected $fillable = [
        'arranger_id', 'importer_id', 'date_shipment',
        'carrier_id', 'carrier_date_transfer', 'carrier_means_of_transport',
        'generator_id', 'recovery_id',
        'recovery_operation_id', 'waste_description_id', 'waste_code_id',
        'status', 'pdf_path',
    ];

    protected $casts = [
        'date_shipment'         => 'date',
        'carrier_date_transfer' => 'date',
    ];

    // Pole 1
    public function arranger(): BelongsTo
    {
        return $this->belongsTo(Annex7Contractor::class, 'arranger_id');
    }

    // Pole 2
    public function importer(): BelongsTo
    {
        return $this->belongsTo(Annex7Contractor::class, 'importer_id');
    }

    // Pole 5
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Annex7Contractor::class, 'carrier_id');
    }

    // Pole 6
    public function generator(): BelongsTo
    {
        return $this->belongsTo(Annex7Contractor::class, 'generator_id');
    }

    // Pole 7
    public function recovery(): BelongsTo
    {
        return $this->belongsTo(Annex7Contractor::class, 'recovery_id');
    }

    // Pole 8
    public function recoveryOperation(): BelongsTo
    {
        return $this->belongsTo(Annex7RecoveryOperation::class, 'recovery_operation_id');
    }

    // Pole 9
    public function wasteDescription(): BelongsTo
    {
        return $this->belongsTo(Annex7WasteDescription::class, 'waste_description_id');
    }

    // Pole 10
    public function wasteCode(): BelongsTo
    {
        return $this->belongsTo(WasteCode::class, 'waste_code_id');
    }
}
