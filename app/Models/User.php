<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dni',
        'name',
        'email',
        'telefono',
        'rol_id',
        'cuadrilla_id',
        'password',
        'ver_privada',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Relación muchos a muchos con areas
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_user');
    }

    /**
     * Relación muchos a uno con rol
     * Un usuario pertenece a un rol
     */
    public function rol()
    {
        return $this->belongsTo(UserRol::class, 'rol_id');
    }
    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class, 'cuadrilla_id');
    }

    /**
     * Override para usar DNI en lugar de email para autenticación
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public static function getUsuariosDeAreas()
    {
        $usuarioLogueado = Auth::user();
        $userAreasLogueado = $usuarioLogueado->areas->pluck('id')->toArray();
        
        // Si el usuario logueado no tiene áreas asignadas, mostrar todos los usuarios
        if (empty($userAreasLogueado)) {
            return self::with(['areas', 'rol'])
                ->where('ver_privada', $usuarioLogueado->ver_privada)
                ->orderBy('name')
                ->get();
        }
        
        // Si tiene áreas asignadas, solo mostrar usuarios de esas áreas (las del logueado)
        return self::with(['areas', 'rol'])
            ->where('ver_privada', $usuarioLogueado->ver_privada)
            ->whereHas('areas', function($q) use ($userAreasLogueado) {
                $q->whereIn('areas.id', $userAreasLogueado);
            })
            ->orderBy('name')
            ->get();
    }
}