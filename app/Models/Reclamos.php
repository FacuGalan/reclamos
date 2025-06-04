<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamos extends Model
{
    //
    protected $table = 'reclamos';
    protected $fillable = [
        'fecha',
        'observacion',
        'direccion',
        'coordenadas',
        'id_area'
    ];

    public function area()
    {
        return $this->belongsTo(Areas::class, 'id_area');
    }
}
