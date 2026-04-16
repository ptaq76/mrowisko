<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lieferschein extends Model
{
    protected $table = 'lieferscheins';

    protected $fillable = [
        'number',
        'importer_id',
        'client_id',
        'goods_id',
        'waste_code_id',
        'date',
        'time_window',
        'goods_description',
        'transp_zew',
        'status',
        'pdf_path',
        'is_used',
    ];

    protected $casts = [
        'date' => 'date',
        'transp_zew' => 'boolean',
        'status' => 'boolean',
        'is_used' => 'boolean',
    ];

    public function importer()
    {
        return $this->belongsTo(Importer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function wasteCode()
    {
        return $this->belongsTo(WasteCode::class);
    }

    public function goods()
    {
        return $this->belongsTo(LsGoods::class, 'goods_id');
    }
}
