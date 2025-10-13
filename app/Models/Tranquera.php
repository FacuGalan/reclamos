<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tranquera extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'tr_tranqueras';

    /**
     * Clave primaria personalizada
     */
    protected $primaryKey = 'tranquera';

    /**
     * Tipo de clave primaria
     */
    protected $keyType = 'int';

    /**
     * Indica si la clave primaria es auto-incrementable
     */
    public $incrementing = false;

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'tranquera',
        'domicilio',
        'detalle',
        'observa',
        'puntomapa'
    ];

    /**
     * Campos que deben ser tratados como fechas
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Obtiene las aclaraciones de la tranquera
     * Combina detalle y observa, o devuelve uno solo si son idénticos
     * 
     * @return string
     */
    public function getAclaracionesAttribute()
    {
        $detalle = trim($this->detalle ?? '');
        $observa = trim($this->observa ?? '');
        
        // Si ambos están vacíos
        if (empty($detalle) && empty($observa)) {
            return '';
        }
        
        // Si solo uno tiene contenido
        if (empty($detalle)) {
            return $observa;
        }
        
        if (empty($observa)) {
            return $detalle;
        }
        
        // Si ambos tienen contenido pero son exactamente iguales
        if ($detalle === $observa) {
            return $detalle;
        }
        
        // Si son diferentes, combinarlos con separador
        return $detalle . ' - ' . $observa;
    }

    /**
     * Método estático para buscar una tranquera por número
     * 
     * @param int $numero
     * @return Tranquera|null
     */
    public static function buscarPorNumero($numero)
    {
        return static::where('tranquera', $numero)->first();
    }

    /**
     * Método para verificar si existe una tranquera
     * 
     * @param int $numero
     * @return bool
     */
    public static function existe($numero)
    {
        return static::where('tranquera', $numero)->exists();
    }

    /**
     * Scope para buscar por número
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $numero
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorNumero($query, $numero)
    {
        return $query->where('tranquera', $numero);
    }

    /**
     * Obtener el número de la tranquera como string con formato
     * 
     * @return string
     */
    public function getNumeroFormateadoAttribute()
    {
        return 'Tranquera N° ' . $this->tranquera;
    }

    /**
     * Accessor para obtener el domicilio limpio
     * 
     * @return string
     */
    public function getDomicilioLimpioAttribute()
    {
        return trim($this->domicilio ?? '');
    }

    /**
     * Accessor para obtener las coordenadas desde puntomapa
     * 
     * @return string
     */
    public function getCoordenadasAttribute()
    {
        return trim($this->puntomapa ?? '');
    }

    /**
     * Método para verificar si tiene coordenadas válidas
     * 
     * @return bool
     */
    public function tieneCoordenadasValidas()
    {
        $coordenadas = $this->coordenadas;
        
        if (empty($coordenadas)) {
            return false;
        }
        
        // Verificar si tiene formato de coordenadas (lat,lng)
        $coords = explode(',', $coordenadas);
        
        if (count($coords) !== 2) {
            return false;
        }
        
        $lat = trim($coords[0]);
        $lng = trim($coords[1]);
        
        return is_numeric($lat) && is_numeric($lng);
    }
}