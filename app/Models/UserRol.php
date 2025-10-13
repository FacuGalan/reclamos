<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRol extends Model
{
    use HasFactory;

    protected $fillable = ['nombre',
        'lReclamosAbm',
        'lReclamosAlta',
        'lReclamosModifica',
        'lReclamosBaja',
        'lReclamosDeriva',
        'lReclamosFinaliza',
        'lReportesAbm',
        'lSecretariaAbm',
        'lAreasAbm',
        'lMotivosAbm',
        'lTiposMovAbm',
        'lEstadosAbm',
        'lUsuariosAbm',
        'lPreguntasFrecAbm',
        'lCuadrillasAbm',
        'lEstadisticas'
    ];

    protected $casts = [
        'lReclamosAbm' => 'boolean',
        'lReclamosAlta' => 'boolean',
        'lReclamosModifica' => 'boolean',
        'lReclamosBaja' => 'boolean',
        'lReclamosDeriva' => 'boolean',
        'lReclamosFinaliza' => 'boolean',
        'lReportesAbm' => 'boolean',
        'lSecretariaAbm' => 'boolean',
        'lAreasAbm' => 'boolean',
        'lMotivosAbm' => 'boolean',
        'lTiposMovAbm' => 'boolean',
        'lEstadosAbm' => 'boolean',
        'lUsuariosAbm' => 'boolean',
        'lPreguntasFrecAbm' => 'boolean',
        'lCuadrillasAbm' => 'boolean',
        'lEstadisticas' => 'boolean'
    ];

    /**
     * Relación uno a muchos con usuarios
     * Un rol puede tener muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}