<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuadrilla extends Model
{
    protected $fillable = ['id','nombre','area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
