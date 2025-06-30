<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Ruta principal que carga la vista home en el slot del welcome
Route::get('/', function () {
    return view('welcome', ['slot' => view('home')]);
})->name('home');

// Ruta para nuevo reclamo que carga la vista nuevo-reclamo en el slot
Route::get('/nuevo-reclamo', function () {
    return view('welcome', ['slot' => view('reclamos/nuevo-reclamo')]);
})->name('nuevo-reclamo');

// Rutas para las otras secciones (las crearemos después)
Route::get('/nuevo-reporte', function () {
    return view('welcome', ['slot' => view('nuevo-reporte')]);
})->name('nuevo-reporte');

Route::get('/tramites', function () {
    return view('welcome', ['slot' => view('tramites')]);
})->name('tramites');

// Rutas del dashboard (área privada)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('reclamos', 'reclamos')
    ->middleware(['auth', 'verified'])
    ->name('reclamos');

Route::view('areas', 'areas')
    ->middleware(['auth', 'verified'])
    ->name('areas');

// Rutas de configuración
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';