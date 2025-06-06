<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    //
    protected $fillable = ['tipo_movimiento_id', 'fecha', 'observaciones', 'estado_id', 'usuario_id'];
    public function tipoMovimiento()
    {
        return $this->belongsTo(Tipo_movimiento::class, 'tipo_movimiento_id');
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
