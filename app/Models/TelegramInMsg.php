<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramInMsg extends Model
{
    protected $table = 'telegram_in_msg';

    protected $fillable = [
        'telegram_user_id',
        'telegram_message_id',
        'username',
        'first_name',
        'last_name',
        'language_code',
        'text',
        'command',
        'is_start',
        'bot_token_hash',
        'payload',
        'received_at',
    ];

    protected $casts = [
        'is_start' => 'boolean',
        'payload' => 'array',
        'received_at' => 'datetime',
    ];
}
