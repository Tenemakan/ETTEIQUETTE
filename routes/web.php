<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtiquetteController;

Route::get('/', function () {
    return view('etiquette/index');
});

Route::get('/etiquette', [EtiquetteController::class, 'index'])->name('etiquette.index');
Route::post('/generer', [EtiquetteController::class, 'generer'])->name('etiquette.generer');
