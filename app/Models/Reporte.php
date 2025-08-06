<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    //
    protected $fillable = [
        'categoria_id',
        'fecha',
        'persona_id',
        'coordenadas',
        'domicilio_id',
        'habitual',
        'observaciones'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    public function domicilio()
    {
        return $this->belongsTo(Domicilios::class, 'domicilio_id');
    }
    public function categoria()
    {
        return $this->belongsTo(ReporteCategoria::class, 'categoria_id');
    }
}
