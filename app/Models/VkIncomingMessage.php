<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VkIncomingMessage extends Model
{
    protected $table = 'vk_incoming_messages';

    protected $fillable = [
        'channel',
        'payload',
        'is_delivered',
        'delivered_at',
        'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_delivered' => 'boolean',
        'delivered_at' => 'datetime',
        'received_at' => 'datetime',
    ];
}
