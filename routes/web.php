<?php

use App\Http\Controllers\Api\WhoisController;
use App\Livewire\IndexComponent;
use App\Livewire\LaravelLogComponent;
use App\Livewire\TelegramLogComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexComponent::class)->name('index');
Route::get('/telegram/log', TelegramLogComponent::class)->name('telegram.log');
Route::get('/laravel/log', LaravelLogComponent::class)->name('laravel.log');

// старые методы
Route::get('/whois.php', WhoisController::class)->name('legacy.whois');
