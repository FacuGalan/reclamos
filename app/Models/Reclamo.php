<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reclamo extends Model
{
    //
    protected $fillable = [
        'fecha', 
        'descripcion',        
        'direccion',            
        'entre_calles',         
        'observaciones', 
        'estado_id', 
        'coordenadas', 
        'usuario_id',
        'area_id',          
        'categoria_id',       
        'responsable_id', 
        'notificado',
        'no_aplica',     
        'persona_id',           
        'domicilio_id',
        'barrio_id',
        'edificio_id',       
    ];
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'reclamo_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function domicilio()
    {
        return $this->belongsTo(Domicilios::class, 'domicilio_id');
    }
    public function barrio()
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }
    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'edificio_id');
    }
    
    // NUEVOS MÉTODOS PARA ESTADÍSTICAS

    /**
     * Scope para filtrar reclamos con coordenadas válidas
     */
    public function scopeConCoordenadas($query)
    {
        return $query->whereNotNull('coordenadas')
                    ->where('coordenadas', '!=', '')
                    ->where('coordenadas', 'REGEXP', '^-?[0-9]+\.?[0-9]*,-?[0-9]+\.?[0-9]*$');
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaDesde = null, $fechaHasta = null)
    {
        if ($fechaDesde) {
            $query->where('fecha', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha', '<=', $fechaHasta);
        }
        
        return $query;
    }

    /**
     * Scope para filtrar por áreas del usuario
     */
    public function scopeEnAreas($query, array $areaIds)
    {
        return $query->whereIn('area_id', $areaIds);
    }

    /**
     * Obtener las coordenadas como array [lat, lng]
     */
    public function getCoordenadasArrayAttribute(): ?array
    {
        if (!$this->coordenadas || !str_contains($this->coordenadas, ',')) {
            return null;
        }

        $coords = explode(',', $this->coordenadas);
        if (count($coords) !== 2) {
            return null;
        }

        $lat = trim($coords[0]);
        $lng = trim($coords[1]);

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }

        return [
            'lat' => (float) $lat,
            'lng' => (float) $lng
        ];
    }

    /**
     * Verificar si el reclamo tiene coordenadas válidas
     */
    public function tieneCoordenadasValidas(): bool
    {
        return $this->coordenadas_array !== null;
    }

    /**
     * Obtener la distancia desde un punto específico (en kilómetros)
     * Usa la fórmula de Haversine
     */
    public function distanciaDesde(float $latitud, float $longitud): ?float
    {
        $coords = $this->coordenadas_array;
        if (!$coords) {
            return null;
        }

        $radioTierra = 6371; // Radio de la Tierra en km

        $dLat = deg2rad($coords['lat'] - $latitud);
        $dLng = deg2rad($coords['lng'] - $longitud);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($latitud)) * cos(deg2rad($coords['lat'])) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }

    /**
     * Scope para reclamos dentro de un radio específico
     */
    public function scopeDentroDelRadio($query, float $latitud, float $longitud, float $radioKm)
    {
        // Aproximación usando degrees (1 grado ≈ 111 km)
        $gradoEnKm = 111;
        $deltaGrados = $radioKm / $gradoEnKm;

        return $query->conCoordenadas()
                    ->whereRaw("
                        (6371 * acos(
                            cos(radians(?)) 
                            * cos(radians(SUBSTRING_INDEX(coordenadas, ',', 1))) 
                            * cos(radians(SUBSTRING_INDEX(coordenadas, ',', -1)) - radians(?)) 
                            + sin(radians(?)) 
                            * sin(radians(SUBSTRING_INDEX(coordenadas, ',', 1)))
                        )) <= ?
                    ", [$latitud, $longitud, $latitud, $radioKm]);
    }

    /**
     * Obtener estadísticas básicas del reclamo
     */
    public function getEstadisticasBasicas(): array
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha->format('Y-m-d'),
            'categoria' => $this->categoria?->nombre ?? 'Sin categoría',
            'area' => $this->area?->nombre ?? 'Sin área',
            'estado' => $this->estado?->nombre ?? 'Sin estado',
            'barrio' => $this->barrio?->nombre ?? 'Sin barrio',
            'coordenadas' => $this->coordenadas_array,
            'dias_desde_creacion' => $this->created_at->diffInDays(now()),
            'tiene_responsable' => !is_null($this->responsable_id),
            'notificado' => $this->notificado ?? false
        ];
    }

    /**
     * Scope para obtener estadísticas agrupadas por período
     */
    public function scopeEstadisticasPorPeriodo($query, string $periodo = 'month')
    {
        $formato = match($periodo) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m'
        };

        return $query->selectRaw("
                DATE_FORMAT(fecha, '{$formato}') as periodo,
                COUNT(*) as total_reclamos,
                COUNT(CASE WHEN estado_id = 4 THEN 1 END) as finalizados,
                COUNT(CASE WHEN estado_id = 5 THEN 1 END) as cancelados,
                COUNT(CASE WHEN responsable_id IS NOT NULL THEN 1 END) as asignados
            ")
            ->groupBy('periodo')
            ->orderBy('periodo');
    }
}
