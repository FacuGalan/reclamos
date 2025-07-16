<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilios extends Model
{
    //
    protected $fillable = ['persona_id','direccion', 'entre_calles', 'coordenadas','barrio_id'];
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    public function barrio()
    {
        return $this->belongsTo(Barrios::class, 'barrio_id');
    }
}
