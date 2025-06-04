<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    //
    protected $table = 'areas'; 
    protected $fillable = [
        'nombre'
    ];

    public function reclamos()
    {
        return $this->hasMany(Reclamos::class, 'id_area');
    }
}
