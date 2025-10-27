<?php

namespace App\Traits;

use App\Models\Reclamo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ManejadorEstadisticas
{
    /**
     * Generar estadísticas de reclamos por categoría
     */
    protected function estadisticasPorCategoria(Collection $reclamos, int $limite = 10): Collection
    {
        return $reclamos->groupBy('categoria.nombre')
                      ->map->count()
                      ->sortDesc()
                      ->take($limite);
    }

    /**
     * Generar estadísticas de reclamos por área
     */
    protected function estadisticasPorArea(Collection $reclamos, int $limite = 10): Collection
    {
        return $reclamos->groupBy('area.nombre')
                      ->map->count()
                      ->sortDesc()
                      ->take($limite);
    }

    /**
     * Generar estadísticas de reclamos por estado
     */
    protected function estadisticasPorEstado(Collection $reclamos): Collection
    {
        return $reclamos->groupBy('estado.nombre')
                      ->map->count()
                      ->sortDesc();
    }

    /**
     * Generar estadísticas de reclamos por edificio
     */
    protected function estadisticasPorEdificio(Collection $reclamos, int $limite = 10): Collection
    {
        return $reclamos->filter(fn($r) => $r->edificio)
                      ->groupBy('edificio.nombre')
                      ->map->count()
                      ->sortDesc()
                      ->take($limite);
    }   

    /**
     * Generar estadísticas de reclamos por cuadrilla
     */
    protected function estadisticasPorCuadrilla(Collection $reclamos, int $limite = 10): Collection
    {
        return $reclamos->filter(fn($r) => $r->categoria && $r->categoria->cuadrilla)
                      ->groupBy('categoria.cuadrilla.nombre')
                      ->map->count()
                      ->sortDesc()
                      ->take($limite);
    }   

    /**
     * Generar estadísticas temporales (por mes)
     */
    protected function estadisticasTemporales(Collection $reclamos, string $formato = 'Y-m'): Collection
    {
        return $reclamos->groupBy(function ($reclamo) use ($formato) {
                return \Carbon\Carbon::parse($reclamo->fecha)->format($formato);
            })
            ->map->count()
            ->sortKeys();
    }

    /**
     * Generar estadísticas de densidad por área geográfica
     */
    protected function estadisticasDensidad(Collection $reclamos, float $gridSize = 0.01): array
    {
        $densidad = [];
        $bounds = $this->calcularBounds($reclamos);
        
        if (!$bounds) {
            return [];
        }

        // Crear grid
        $latMin = floor($bounds['lat_min'] / $gridSize) * $gridSize;
        $latMax = ceil($bounds['lat_max'] / $gridSize) * $gridSize;
        $lngMin = floor($bounds['lng_min'] / $gridSize) * $gridSize;
        $lngMax = ceil($bounds['lng_max'] / $gridSize) * $gridSize;

        for ($lat = $latMin; $lat <= $latMax; $lat += $gridSize) {
            for ($lng = $lngMin; $lng <= $lngMax; $lng += $gridSize) {
                $count = $this->contarReclamosEnArea($reclamos, $lat, $lng, $gridSize);
                if ($count > 0) {
                    $densidad[] = [
                        'lat' => round($lat + $gridSize/2, 6),
                        'lng' => round($lng + $gridSize/2, 6),
                        'intensidad' => $count,
                        'peso' => min($count / 5.0, 1.0) // Normalizar peso
                    ];
                }
            }
        }

        return $densidad;
    }

    /**
     * Calcular bounds (límites) de las coordenadas
     */
    private function calcularBounds(Collection $reclamos): ?array
    {
        $coordenadas = $reclamos->map(function ($reclamo) {
            if (!$reclamo->coordenadas || !str_contains($reclamo->coordenadas, ',')) {
                return null;
            }
            
            $coords = explode(',', $reclamo->coordenadas);
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
        })->filter();

        if ($coordenadas->isEmpty()) {
            return null;
        }

        return [
            'lat_min' => $coordenadas->min('lat'),
            'lat_max' => $coordenadas->max('lat'),
            'lng_min' => $coordenadas->min('lng'),
            'lng_max' => $coordenadas->max('lng')
        ];
    }

    /**
     * Contar reclamos en un área específica del grid
     */
    private function contarReclamosEnArea(Collection $reclamos, float $latBase, float $lngBase, float $gridSize): int
    {
        return $reclamos->filter(function ($reclamo) use ($latBase, $lngBase, $gridSize) {
            if (!$reclamo->coordenadas || !str_contains($reclamo->coordenadas, ',')) {
                return false;
            }
            
            $coords = explode(',', $reclamo->coordenadas);
            if (count($coords) !== 2) {
                return false;
            }
            
            $lat = (float) trim($coords[0]);
            $lng = (float) trim($coords[1]);
            
            return $lat >= $latBase && $lat < ($latBase + $gridSize) &&
                   $lng >= $lngBase && $lng < ($lngBase + $gridSize);
        })->count();
    }

    /**
     * Generar resumen estadístico completo
     */
    protected function generarResumenCompleto(Collection $reclamos): array
    {
        $total = $reclamos->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'por_categoria' => collect(),
                'por_area' => collect(),
                'por_estado' => collect(),
                'por_mes' => collect(),
                'por_barrio' => collect(),
                'por_cuadrilla' => collect(),
                'por_edificio' => collect(),
                'promedio_por_dia' => 0,
                'tiempo_promedio_resolucion' => 0,
                'porcentaje_finalizados' => 0
            ];
        }

        // Estadísticas básicas
        $porCategoria = $this->estadisticasPorCategoria($reclamos, 5);
        $porArea = $this->estadisticasPorArea($reclamos, 5);
        $porEstado = $this->estadisticasPorEstado($reclamos);
        $porMes = $this->estadisticasTemporales($reclamos);
        $porEdificio = $this->estadisticasPorEdificio($reclamos);
        $porCuadrilla = $this->estadisticasPorCuadrilla($reclamos);

        // Estadísticas por barrio
        $porBarrio = $reclamos->filter(fn($r) => $r->barrio)
                            ->groupBy('barrio.nombre')
                            ->map->count()
                            ->sortDesc()
                            ->take(5);

        // Calcular promedios
        $fechas = $reclamos->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f));
        $diasTotal = $fechas->min()->diffInDays($fechas->max()) + 1;
        $promedioPorDia = $diasTotal > 0 ? round($total / $diasTotal, 2) : 0;

        // Porcentaje de finalizados
        $finalizados = $reclamos->where('estado_id', 4)->count();
        $porcentajeFinalizados = $total > 0 ? round(($finalizados / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'por_categoria' => $porCategoria,
            'por_area' => $porArea,
            'por_estado' => $porEstado,
            'por_mes' => $porMes,
            'por_barrio' => $porBarrio,
            'por_cuadrilla' => $porCuadrilla,
            'por_edificio' => $porEdificio,
            'promedio_por_dia' => $promedioPorDia,
            'porcentaje_finalizados' => $porcentajeFinalizados,
            'periodo_analizado' => [
                'desde' => $fechas->min()->format('d/m/Y'),
                'hasta' => $fechas->max()->format('d/m/Y'),
                'dias_total' => $diasTotal
            ]
        ];
    }

    /**
     * Obtener estadísticas de rendimiento
     */
    protected function estadisticasRendimiento(array $userAreas, ?string $fechaDesde = null, ?string $fechaHasta = null,
                                           ?string $categoria, ?string $area, ?string $barrio, ?string $cuadrilla, ?string $edificio): array
    {
        $query = Reclamo::with(['categoria'])
            ->whereHas('categoria', function ($q) {
                $q->where('privada', Auth::user()->ver_privada);
            })
            ->whereIn('area_id', $userAreas);

        if ($fechaDesde) {
            $query->where('fecha', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->where('fecha', '<=', $fechaHasta);
        }

        if ($categoria) {
            $query->where('categoria_id', $categoria);
        }

        if ($area) {
            $query->where('area_id', $area);
        }

        if ($barrio) {
            $query->where('barrio_id', $barrio);
        }

        if (!empty($cuadrilla)) {
            $query->whereHas('categoria', function ($q) use ($cuadrilla) {
                $q->where('cuadrilla_id', $cuadrilla);
            });
        }

        if ($edificio) {
            $query->where('edificio_id', $edificio);
        }

        // Usar consultas agregadas para mejor rendimiento
        $stats = $query->selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN estado_id = 4 THEN 1 END) as finalizados,
            COUNT(CASE WHEN estado_id = 5 THEN 1 END) as cancelados,
            COUNT(CASE WHEN estado_id  < 4 OR estado_id > 5 THEN 1 END) as activos,
            COUNT(CASE WHEN responsable_id IS NOT NULL THEN 1 END) as asignados,
            COUNT(CASE WHEN coordenadas IS NOT NULL AND coordenadas != "" THEN 1 END) as con_ubicacion,
            AVG(CASE WHEN estado_id = 4 THEN DATEDIFF(updated_at, created_at) END) as promedio_dias_resolucion
        ')->first();    

        // Redondear el promedio de días de resolución
        $promedioDiasResolucion = $stats->promedio_dias_resolucion 
            ? round($stats->promedio_dias_resolucion, 1) 
            : 0;

        return [
            'total_reclamos' => $stats->total ?? 0,
            'finalizados' => $stats->finalizados ?? 0,
            'cancelados' => $stats->cancelados ?? 0,
            'activos' => $stats->activos ?? 0,
            'asignados' => $stats->asignados ?? 0,
            'con_ubicacion' => $stats->con_ubicacion ?? 0,
            'sin_asignar' => ($stats->total ?? 0) - ($stats->asignados ?? 0),
            'promedio_dias_resolucion' => $promedioDiasResolucion,
            'porcentaje_finalizados' => $stats->total > 0 ? round(($stats->finalizados / $stats->total) * 100, 1) : 0,
            'porcentaje_con_ubicacion' => $stats->total > 0 ? round(($stats->con_ubicacion / $stats->total) * 100, 1) : 0
        ];
    }
}