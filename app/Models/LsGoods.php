<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LsGoods extends Model
{
    protected $table = 'ls_goods';

    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
