<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['nombre','secretaria_id'];
    
    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class);
    }

    /**
     * Relación muchos a muchos con usuarios
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'area_user');
    }

    /**
     * Relación uno a muchos con categorías
     */
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }
    /**
     * Relación uno a muchos con tipos de movimiento
     */
    public function tiposMovimiento()
    {
        return $this->hasMany(TipoMovimiento::class);   
    }
}