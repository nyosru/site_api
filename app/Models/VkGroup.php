<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VkGroup extends Model
{
    protected $table = 'vk_group';

    protected $fillable = [
        'group_name',
        'token',
        'payed',
        'payed_date',
    ];

    protected $casts = [
        'payed' => 'boolean',
        'payed_date' => 'date',
    ];
}
