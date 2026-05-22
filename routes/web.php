<?php

use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\WhoisController;
use App\Livewire\IndexComponent;
use App\Livewire\LaravelLogComponent;
use App\Livewire\TelegramLogComponent;
use App\Livewire\VkChannelComponent;
use App\Livewire\VkIncomingLogComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexComponent::class)->name('index');
Route::get('/telegram/log', TelegramLogComponent::class)->name('telegram.log');
Route::get('/laravel/log', LaravelLogComponent::class)->name('laravel.log');
Route::get('/vk/incoming/log', VkIncomingLogComponent::class);
Route::get('/vk/channels', VkChannelComponent::class);

Route::get('telegram.php', TelegramController::class )->name('laravel.log');



// старые методы
Route::get('/whois.php', WhoisController::class)->name('legacy.whois');
