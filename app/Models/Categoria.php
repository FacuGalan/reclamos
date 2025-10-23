<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre','nombre_publico','area_id', 'privada', 'activo', 'cuadrilla_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }
}
