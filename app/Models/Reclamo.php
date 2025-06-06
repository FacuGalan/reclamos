<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    //
    protected $fillable = ['fecha', 'observaciones', 'estado_id', 'coordenadas', 'usuario_id'];
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
