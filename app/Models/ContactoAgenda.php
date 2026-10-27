<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactoAgenda extends Model
{
    use SoftDeletes;

    protected $table = 'contactos_agenda';

    protected $fillable = [
        'nombre',
        'telefono',
        'ocupacion',
    ];

    public function reclamos()
    {
        return $this->hasMany(Reclamo::class, 'contacto_agenda_id');
    }
}
