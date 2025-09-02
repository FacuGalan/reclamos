<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    //

    protected $table = 'preguntas';

    protected $fillable = [
        'pregunta',
        'respuesta',
        'area_id'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

}
