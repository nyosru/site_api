<?php

use App\Http\Controllers\Api\WhoisController;
use App\Livewire\IndexComponent;
use App\Livewire\TelegramLogComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexComponent::class)->name('index');
Route::get('/telegram/log', TelegramLogComponent::class)->name('telegram.log');

// старые методы
Route::get('/whois.php', WhoisController::class)->name('legacy.whois');
