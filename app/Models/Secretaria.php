<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model
{
    //
    protected $fillable = ['nombre'];
    
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
