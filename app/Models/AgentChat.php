<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentChat extends Model
{
    protected $fillable = ['title', 'messages', 'user_id'];

    protected $casts = [
        'messages' => 'array',
    ];
}
