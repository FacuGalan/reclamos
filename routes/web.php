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

Route::view('reclamos', 'reclamos')
    ->middleware(['auth', 'verified'])
    ->name('reclamos');

    // Crear nuevo reclamo normal (área privada)
Route::get('reclamos/create', function () {
    return view('reclamos.create', ['tipoInterno' => false]);
})
    ->middleware(['auth', 'verified'])
    ->name('reclamos.create');

// Crear nuevo reclamo interno (área privada)  
Route::get('reclamos/create/interno', function () {
    return view('reclamos.create', ['tipoInterno' => true]);
})
    ->middleware(['auth', 'verified'])
    ->name('reclamos.create.interno');

// Ruta para modificar reclamo, recibe el ID del reclamo y un parámetro opcional editable
Route::view('modificar-reclamo/{reclamo}', 'modificar-reclamo')
    ->middleware(['auth', 'verified'])
    ->name('modificar-reclamo');

// Rutas para las otras secciones (las crearemos después)
Route::get('/nuevo-reporte', function () {
    return view('welcome', ['slot' => view('nuevo-reporte')]);
})->name('nuevo-reporte');

Route::get('/tramites', function () {
    return view('welcome', ['slot' => view('tramites')]);
})->name('tramites');

// Rutas del dashboard (área privada)
Route::view('dashboard', 'reclamos')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('reclamos', 'reclamos')
    ->middleware(['auth', 'verified'])
    ->name('reclamos');

Route::view('secretarias', 'secretarias')
    ->middleware(['auth', 'verified'])
    ->name('secretarias');
    
Route::view('areas', 'areas')
    ->middleware(['auth', 'verified'])
    ->name('areas');

Route::view('tipos-movimiento', 'tipos-movimiento')
    ->middleware(['auth', 'verified'])
    ->name('tipos-movimiento');

Route::view('estados', 'estados')
    ->middleware(['auth', 'verified'])
    ->name('estados');


// Rutas de configuración
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';