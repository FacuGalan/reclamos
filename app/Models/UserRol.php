<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRol extends Model
{
    protected $fillable = ['nombre'];

    /**
     * Relación uno a muchos con usuarios
     * Un rol puede tener muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}