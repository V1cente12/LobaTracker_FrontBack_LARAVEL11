<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Livewire\GameView;
use App\Livewire\Dashboard;

Route::get('/', function () {
    return view('auth.login');
});

// Usa esta ruta para Livewire
Route::get('/game/{gameId}', GameView::class)->name('game.view');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
