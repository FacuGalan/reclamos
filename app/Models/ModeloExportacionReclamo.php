<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeloExportacionReclamo extends Model
{
    protected $table = 'modelos_exportacion_reclamos';

    protected $fillable = [
        'nombre',
        'area_id',
        'campos',
        'usuario_creador_id',
    ];

    protected $casts = [
        'campos' => 'array',
    ];

    /**
     * Relación con el área
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con el usuario creador
     */
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }
}
