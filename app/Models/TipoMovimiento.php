<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    //
    protected $fillable = ['nombre', 'area_id', 'estado_id'];
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
