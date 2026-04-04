<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    protected $fillable = [
        'client_id',
        'category',
        'name',
        'email',
        'phone',
    ];

    const CATEGORIES = [
        'awizacje' => 'Awizacje',
        'faktury'  => 'Faktury',
        'handlowe' => 'Handlowe',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
