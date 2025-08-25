<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdenImpresionController extends Controller
{
    public function imprimir()
    {
        // Obtener los datos de la sesión
        $datos = session('datos_orden_impresion');
        
        if (!$datos) {
            return redirect()->back()->with('error', 'No se encontraron datos para imprimir');
        }
        
        // Limpiar los datos de la sesión después de usarlos
        session()->forget('datos_orden_impresion');
        
        // Retornar la vista de impresión
        return view('orden-impresion', compact('datos'));
    }
}