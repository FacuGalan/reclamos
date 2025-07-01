<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = ['nombre', 'codigo_color', 'color_texto'];

    /**
     * Relación uno a muchos con tipos de movimiento
     */
    public function tiposMovimiento()
    {
        return $this->hasMany(TipoMovimiento::class);   
    }

    /**
     * Obtener los colores predefinidos para estados
     */
    public static function getColoresPredefinidos()
    {
        return [
            '#FEF3C7' => 'Amarillo (Pendiente)',
            '#DBEAFE' => 'Azul (En tramite)', 
            '#D1FAE5' => 'Verde (Finalizado)',
            '#FEE2E2' => 'Rojo (Cancelado)',
            '#F3F4F6' => 'Gris (Cerrado)',
            '#F3E8FF' => 'Púrpura (Revisión)',
            '#FDF2F8' => 'Rosa (Pendiente Aprobación)',
            '#ECFCCB' => 'Verde Claro (Completado)',
        ];
    }

    /**
     * Obtener el color del texto basado en el color de fondo
     */
    public function getColorTexto()
    {
        // Si tiene color personalizado, usarlo
        if ($this->color_texto) {
            return $this->color_texto;
        }

        // Si no, usar los colores predefinidos
        $coloresTexto = [
            '#FEF3C7' => '#92400E', // amarillo -> texto marrón
            '#DBEAFE' => '#1E40AF', // azul -> texto azul oscuro
            '#D1FAE5' => '#065F46', // verde -> texto verde oscuro
            '#F3F4F6' => '#374151', // gris -> texto gris oscuro
            '#FEE2E2' => '#991B1B', // rojo -> texto rojo oscuro
            '#F3E8FF' => '#6B21A8', // púrpura -> texto púrpura oscuro
            '#FDF2F8' => '#BE185D', // rosa -> texto rosa oscuro
            '#ECFCCB' => '#365314', // verde claro -> texto verde muy oscuro
        ];

        return $coloresTexto[$this->codigo_color] ?? '#374151';
    }
}