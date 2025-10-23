<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reporte extends Model
{
    protected $fillable = [
        'categoria_id',
        'fecha',
        'persona_id',
        'coordenadas',
        'domicilio_id',
        'habitual',
        'observaciones',
        'estado_id'
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function domicilio(): BelongsTo
    {
        return $this->belongsTo(Domicilios::class, 'domicilio_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(ReporteCategoria::class, 'categoria_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(ReporteEstado::class, 'estado_id');
    }

    public function estadoCambios(): HasMany
    {
        return $this->hasMany(ReporteEstadoCambio::class, 'reporte_id');
    }

    // Scope para reportes no finalizados
    public function scopeNoFinalizados($query)
    {
        return $query->whereHas('estado', function($q) {
            $q->where('finalizacion', false);
        })->orWhereNull('estado_id');
    }

    // Scope para reportes finalizados
    public function scopeFinalizados($query)
    {
        return $query->whereHas('estado', function($q) {
            $q->where('finalizacion', true);
        });
    }

    // Método para cambiar el estado
    public function cambiarEstado($nuevoEstadoId, $observaciones = null)
    {
        $estadoAnteriorId = $this->estado_id;
        
        $this->estado_id = $nuevoEstadoId;
        $this->save();

        // Registrar el cambio en el historial
        ReporteEstadoCambio::create([
            'reporte_id' => $this->id,
            'estado_anterior_id' => $estadoAnteriorId,
            'estado_nuevo_id' => $nuevoEstadoId,
            'usuario_id' => auth()->id(),
            'observaciones' => $observaciones
        ]);

        return $this;
    }
}