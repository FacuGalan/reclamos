<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilios extends Model
{
    //
    protected $fillable = ['persona_id','direccion', 'entre_calles','direccion_rural','numero_tranquera','coordenadas','barrio_id'];
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    public function barrio()
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }
    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'edificio_id');
    }
    public function tranquera()
    {
        return $this->belongsTo(Tranquera::class, 'numero_tranquera', 'tranquera');
    }
}
