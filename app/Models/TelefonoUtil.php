<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelefonoUtil extends Model
{
    use HasFactory;

    protected $table = 'telefonos_utiles';

    protected $fillable = ['nombre', 'telefono'];
    
}