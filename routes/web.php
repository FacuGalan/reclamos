<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Http\Request;

// Ruta principal que carga la vista home en el slot del welcome
Route::get('/', function () {
    return view('welcome', ['slot' => view('home')]);
})->name('home');

Route::get('reclamos/crear-interno-publico', function (Request $request) {
    // Validar que vengan los parámetros mínimos requeridos
    $request->validate([
        'user' => 'required|numeric|digits:8',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255'
    ]);

    // Guardar los datos en la sesión
    session([
        'datos_reclamo_externo' => [
            'dni' => $request->get('user'),
            'nombre' => $request->get('nombre'),
            'apellido' => $request->get('apellido')
        ]
    ]);

    // Redirigir a una URL limpia
    return redirect()->route('reclamos.crear-interno-publico.form');
})
    ->name('reclamos.crear-interno-publico');

// Nueva ruta para mostrar el formulario con URL limpia (ESTO HACE QUE NO SE MUESTREN EN LA URL LOS DATOS DEL PARAMETRO GET)
Route::get('reclamos/crear-interno-publico/formulario', function () {
    // Recuperar los datos de la sesión
    $datos = session('datos_reclamo_externo');
    
    // Si no hay datos, redirigir a algún lugar (opcional)
    if (!$datos) {
        return redirect()->route('reclamos')->with('error', 'No se encontraron datos para crear el reclamo');
    }

    // Limpiar los datos de la sesión después de usarlos (opcional)
    session()->forget('datos_reclamo_externo');

    return view('welcome', [
        'slot' => view('reclamos/crear-interno-publico', $datos)
    ]);
})
    ->name('reclamos.crear-interno-publico.form');

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

// REPORTES
Route::view('reportes', 'reportes')
    ->middleware(['auth', 'verified'])
    ->name('reportes');

// Rutas para las otras secciones (las crearemos después)
Route::get('/nuevo-reporte', function () {
    return view('welcome', ['slot' => view('reportes/nuevo-reporte')]);
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

Route::view('motivos', 'motivos')
    ->middleware(['auth', 'verified'])
    ->name('motivos');

Route::view('usuarios', 'usuarios')
    ->middleware(['auth', 'verified'])
    ->name('usuarios');

Route::view('tipos-movimiento', 'tipos-movimiento')
    ->middleware(['auth', 'verified'])
    ->name('tipos-movimiento');

Route::view('estados', 'estados')
    ->middleware(['auth', 'verified'])
    ->name('estados');

Route::view('mapa-barrios', 'mapa-barrios')
    ->middleware(['auth', 'verified'])
    ->name('mapa-barrios');

Route::view('estadisticas', 'estadisticas')
    ->middleware(['auth', 'verified'])
    ->name('estadisticas');

// Rutas de configuración
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';