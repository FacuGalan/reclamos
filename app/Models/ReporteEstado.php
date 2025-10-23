<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReporteEstado extends Model
{
    protected $fillable = [
        'nombre',
        'finalizacion'
    ];

    protected $casts = [
        'finalizacion' => 'boolean'
    ];

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'estado_id');
    }

    public function cambios(): HasMany
    {
        return $this->hasMany(ReporteEstadoCambio::class, 'estado_nuevo_id');
    }

    // Scope para estados no finalizados
    public function scopeNoFinalizados($query)
    {
        return $query->where('finalizacion', false);
    }

    // Scope para estados finalizados
    public function scopeFinalizados($query)
    {
        return $query->where('finalizacion', true);
    }
}