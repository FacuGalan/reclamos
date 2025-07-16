<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    protected $fillable = ['nombre', 'poligono'];
    
    // Validar antes de guardar
    public function setPoligonoAttribute($value)
    {
        if ($this->validarPoligono($value)) {
            $this->attributes['poligono'] = $value;
        } else {
            throw new InvalidArgumentException('Formato de polígono inválido');
        }
    }
    
    private function validarPoligono($poligono)
    {
        return $this->validarFormatoWKT($poligono) && 
               $this->validarPoligonoCerrado($poligono) &&
               $this->validarRangoCoordenadas($poligono);
    }
    
    // Obtener coordenadas como array
    public function getCoordenadas()
    {
        preg_match('/POLYGON\(\((.+)\)\)/', $this->poligono, $matches);
        $coordenadas = explode(',', $matches[1]);
        
        return array_map(function($coord) {
            $punto = explode(' ', trim($coord));
            return ['lng' => (float)$punto[0], 'lat' => (float)$punto[1]];
        }, $coordenadas);
    }
}