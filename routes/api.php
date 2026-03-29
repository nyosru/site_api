<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\VkSendMessageController;
use App\Http\Controllers\Api\WhoisController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::get('/whois', WhoisController::class);
Route::any('/telegram', TelegramController::class);
Route::any('/telegram/webhook', TelegramWebhookController::class);
Route::any('/vk/send', VkSendMessageController::class);
