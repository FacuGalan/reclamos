<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estado;

class EstadosColoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Colores predefinidos para estados comunes
        $estadosColores = [
            'Pendiente' => '#FEF3C7',      // Amarillo
            'En Proceso' => '#DBEAFE',      // Azul
            'Resuelto' => '#D1FAE5',        // Verde
            'Cerrado' => '#F3F4F6',         // Gris
            'Rechazado' => '#FEE2E2',       // Rojo
            'Revisión' => '#F3E8FF',        // Púrpura
            'Completado' => '#ECFCCB',      // Verde claro
        ];

        foreach ($estadosColores as $nombre => $color) {
            Estado::where('nombre', $nombre)
                  ->update(['codigo_color' => $color]);
        }

        // Si hay estados sin color asignado, usar gris por defecto
        Estado::whereNull('codigo_color')
              ->orWhere('codigo_color', '')
              ->update(['codigo_color' => '#F3F4F6']);
    }
}