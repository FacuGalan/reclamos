<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    //
    protected $fillable = [
        'fecha', 
        'descripcion',        
        'direccion',            
        'entre_calles',         
        'observaciones', 
        'estado_id', 
        'coordenadas', 
        'usuario_id',
        'area_id',          
        'categoria_id',       
        'responsable_id',      
        'persona_id',           
        'domicilio_id'          
    ];
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
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'reclamo_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function domicilio()
    {
        return $this->belongsTo(Domicilios::class, 'domicilio_id');
    }
}
