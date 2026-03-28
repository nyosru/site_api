<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\WhoisController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::get('/whois', WhoisController::class);
Route::any('/telegram', TelegramController::class);
