<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    //
    protected $fillable = ['nombre'];

    /**
     * Relación uno a muchos con tipos de movimiento
     */
    public function tiposMovimiento()
    {
        return $this->hasMany(TipoMovimiento::class);   
    }
}
