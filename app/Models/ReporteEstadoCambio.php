<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteEstadoCambio extends Model
{
    protected $fillable = [
        'reporte_id',
        'estado_anterior_id',
        'estado_nuevo_id',
        'usuario_id',
        'observaciones'
    ];

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(Reporte::class);
    }

    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(ReporteEstado::class, 'estado_anterior_id');
    }

    public function estadoNuevo(): BelongsTo
    {
        return $this->belongsTo(ReporteEstado::class, 'estado_nuevo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}