<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\WhoisController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::get('/whois', WhoisController::class);
Route::any('/telegram', TelegramController::class);
Route::any('/telegram/webhook', TelegramWebhookController::class);

$text = 'api';
$v1 = file_get_contents("php://input");
$array =
$v = json_decode($v1, true);
$text = $v1;
Log::info('telegram webhook received', [
    'update_id' => $payload['update_id'] ?? null,
    'telegram_user_id' => $from['id'] ?? null,
    'username' => $from['username'] ?? null,
    'command' => $command ?? 11,
    'text' => $text !== '' ? $text : null,
]);

