<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = ['dni', 'nombre','apellido','telefono', 'email'];
    
    public function domicilios()
    {
        return $this->hasMany(Domicilios::class, 'persona_id');
    }
}
