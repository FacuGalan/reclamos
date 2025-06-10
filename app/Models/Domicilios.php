<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilios extends Model
{
    //
    protected $fillable = ['direccion', 'entrecalles', 'coordenadas'];
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
