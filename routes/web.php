<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Livewire\GameView;

Route::get('/', function () {
    return view('welcome');
});



// Usa esta ruta para el controlador si necesitas mostrar la vista con el controlador
// Route::get('/game/{game}', [GameController::class, 'show'])->name('game.view');

// Usa esta ruta para Livewire
Route::get('/game/{gameId}', GameView::class)->name('game.view');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
