<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VkChannel extends Model
{
    protected $table = 'vk_channels';

    protected $fillable = [
        'name',
        'tag',
        'group_id',
        'confirmation_code',
        'secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
