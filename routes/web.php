<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Ruta principal que carga la vista home en el slot del welcome
Route::get('/', function () {
    return view('welcome', ['slot' => view('home')]);
})->name('home');

// Rutas para las otras secciones (las crearemos después)
Route::get('/nuevo-reclamo', function () {
    return view('welcome', ['slot' => view('nuevo-reclamo')]);
})->name('nuevo-reclamo');

Route::get('/consultar-reclamo', function () {
    return view('welcome', ['slot' => view('consultar-reclamo')]);
})->name('consultar-reclamo');

Route::get('/informacion', function () {
    return view('welcome', ['slot' => view('informacion')]);
})->name('informacion');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('reclamos', 'reclamos')
    ->middleware(['auth', 'verified'])
    ->name('reclamos');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';