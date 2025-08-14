<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use App\Models\Categoria;
use App\Models\Area;
use App\Models\Barrio;
use App\Traits\ManejadorEstadisticas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Estadisticas extends Component
{
    use ManejadorEstadisticas;

    public $filtro_fecha_desde = '';
    public $filtro_fecha_hasta = '';
    public $filtro_categoria = '';
    public $filtro_area = '';
    public $filtro_barrio = '';
    
    // Datos para selects
    public $categorias = [];
    public $areas = [];
    public $barrios = [];
    public $userAreas = [];
    
    // Datos del mapa de calor
    public $reclamosCoords = [];
    public $totalReclamos = 0;
    public $resumenEstadisticas = [];
    public $estadisticasRendimiento = [];
    
    // Control de vista
    public $mostrarMapaCalor = false;
    public $cargando = false;

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        // Cargar datos para filtros
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->categorias = Categoria::whereIn('area_id', $this->userAreas)->where('privada', false)->orderBy('nombre')->get();
        $this->barrios = Barrio::orderBy('nombre')->get();
        
        
        // Establecer fechas por defecto (último mes)
        $this->filtro_fecha_hasta = now()->format('Y-m-d');
        $this->filtro_fecha_desde = now()->subMonth()->format('Y-m-d');

        // Cargar estadísticas de rendimiento iniciales
        $this->estadisticasRendimiento = $this->estadisticasRendimiento(
            $this->userAreas, 
            $this->filtro_fecha_desde, 
            $this->filtro_fecha_hasta,
            $this->filtro_categoria,
            $this->filtro_area,
            $this->filtro_barrio
        );

        $this->generarResumenInicial();
    }

    public function generarResumenInicial()
    {
        // Construir query optimizada
            $query = Reclamo::with(['categoria:id,nombre', 'area:id,nombre', 'estado:id,nombre','barrio:id,nombre'])
                ->select([
                    'id', 'coordenadas', 'descripcion', 'direccion', 'fecha', 
                    'categoria_id', 'area_id', 'estado_id','barrio_id'
                ])
                ->whereIn('area_id', $this->userAreas)
                ->whereNotNull('coordenadas')
                ->where('coordenadas', '!=', '');

            // Aplicar filtros de fecha
            if ($this->filtro_fecha_desde) {
                $query->where('fecha', '>=', $this->filtro_fecha_desde);
            }

            if ($this->filtro_fecha_hasta) {
                $query->where('fecha', '<=', $this->filtro_fecha_hasta);
            }

            if ($this->filtro_categoria) {
                $query->where('categoria_id', $this->filtro_categoria);
            }

            if ($this->filtro_area) {
                $query->where('area_id', $this->filtro_area);
            }

            if ($this->filtro_barrio) {
                $query->where('barrio_id', $this->filtro_barrio);
            }

            $query->whereHas('categoria', function ($q) {
                $q->where('privada', false);
            });

            // Obtener reclamos
            $reclamos = $query->get();

            $this->totalReclamos = $reclamos->count();

            // Generar resumen estadístico usando el trait
            $this->resumenEstadisticas = $this->generarResumenCompleto($reclamos);

    }


    public function generarMapaCalor(bool $generarMapa  = false)
    {
        $this->cargando = true;
        
        // Validar fechas
        if ($this->filtro_fecha_desde > $this->filtro_fecha_hasta) {
            session()->flash('error', 'La fecha de inicio no puede ser mayor a la fecha final.');
            $this->cargando = false;
            return;
        }

        // Verificar que hay un rango de fechas razonable
        $diasDiferencia = \Carbon\Carbon::parse($this->filtro_fecha_desde)
            ->diffInDays(\Carbon\Carbon::parse($this->filtro_fecha_hasta));
        
        if ($diasDiferencia > 365) {
            session()->flash('error', 'El rango de fechas no puede ser mayor a 1 año.');
            $this->cargando = false;
            return;
        }

        try {
            // Construir query optimizada
            $query = Reclamo::with(['categoria:id,nombre,privada', 'area:id,nombre', 'estado:id,nombre','barrio:id,nombre'])
                ->select([
                    'id', 'coordenadas', 'descripcion', 'direccion', 'fecha', 
                    'categoria_id', 'area_id', 'estado_id','barrio_id'
                ])
                ->whereIn('area_id', $this->userAreas)
                ->whereNotNull('coordenadas')
                ->where('coordenadas', '!=', '');

            // Aplicar filtros de fecha
            if ($this->filtro_fecha_desde) {
                $query->where('fecha', '>=', $this->filtro_fecha_desde);
            }

            if ($this->filtro_fecha_hasta) {
                $query->where('fecha', '<=', $this->filtro_fecha_hasta);
            }

            if ($this->filtro_categoria) {
                $query->where('categoria_id', $this->filtro_categoria);
            }

            if ($this->filtro_area) {
                $query->where('area_id', $this->filtro_area);
            }

            if ($this->filtro_barrio) {
                $query->where('barrio_id', $this->filtro_barrio);
            }

            $query->whereHas('categoria', function ($q) {
                $q->where('privada', false);
            });

            // Obtener reclamos
            $reclamos = $query->get();

            // Generar resumen estadístico usando el trait
            $this->resumenEstadisticas = $this->generarResumenCompleto($reclamos);

            // Actualizar estadísticas de rendimiento
            $this->estadisticasRendimiento = $this->estadisticasRendimiento(
                $this->userAreas, 
                $this->filtro_fecha_desde, 
                $this->filtro_fecha_hasta,
                $this->filtro_categoria,
                $this->filtro_area,
                $this->filtro_barrio
            );

            if($generarMapa){
                // Procesar coordenadas para el mapa de calor
                $this->reclamosCoords = [];
                foreach ($reclamos as $reclamo) {
                    if ($reclamo->coordenadas && str_contains($reclamo->coordenadas, ',')) {
                        $coords = explode(',', $reclamo->coordenadas);
                        if (count($coords) == 2) {
                            $lat = trim($coords[0]);
                            $lng = trim($coords[1]);
                            
                            if (is_numeric($lat) && is_numeric($lng)) {
                                $this->reclamosCoords[] = [
                                    'id' => $reclamo->id,
                                    'lat' => (float) $lat,
                                    'lng' => (float) $lng,
                                    'descripcion' => substr($reclamo->descripcion, 0, 100),
                                    'direccion' => $reclamo->direccion,
                                    'fecha' => \Carbon\Carbon::parse($reclamo->fecha)->format('d/m/Y'),
                                    'categoria' => $reclamo->categoria->nombre ?? 'Sin categoría',
                                    'area' => $reclamo->area->nombre ?? 'Sin área',
                                    'barrio' => $reclamo->barrio->nombre ?? 'Sin barrio',
                                    'estado' => $reclamo->estado->nombre ?? 'Sin estado'
                                ];
                            }
                        }
                    }
                }

                $this->totalReclamos = count($this->reclamosCoords);

                $this->mostrarMapaCalor = true;

                // Disparar evento para inicializar el mapa
                $this->dispatch('inicializar-mapa-calor', [
                    'reclamos' => $this->reclamosCoords
                ]);

                // Log para debugging
                logger('Mapa de calor generado', [
                    'total_reclamos' => $this->totalReclamos,
                    'filtros' => [
                        'fecha_desde' => $this->filtro_fecha_desde,
                        'fecha_hasta' => $this->filtro_fecha_hasta,
                        'categoria' => $this->filtro_categoria,
                        'area' => $this->filtro_area,
                        'barrio' => $this->filtro_barrio,
                    ]
                ]);

            }else{
                $this->totalReclamos = $reclamos->count();
                $this->mostrarMapaCalor = false;
            }
            

        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el mapa de calor: ' . $e->getMessage());
            logger('Error en mapa de calor:', ['error' => $e->getMessage()]);
        }

        $this->cargando = false;
    }

    public function limpiarFiltros()
    {
        $this->filtro_fecha_desde = now()->subMonth()->format('Y-m-d');
        $this->filtro_fecha_hasta = now()->format('Y-m-d');
        $this->filtro_categoria = '';
        $this->filtro_area = '';
        $this->filtro_barrio = '';
        $this->mostrarMapaCalor = false;
        $this->reclamosCoords = [];
        $this->resumenEstadisticas = [];
        
        // Recargar estadísticas básicas
        $this->estadisticasRendimiento = $this->estadisticasRendimiento(
            $this->userAreas, 
            $this->filtro_fecha_desde, 
            $this->filtro_fecha_hasta,
            $this->filtro_categoria,
            $this->filtro_area,
            $this->filtro_barrio
        );

         $this->generarResumenInicial();
    }

    public function cerrarMapa()
    {
        $this->mostrarMapaCalor = false;
    }

    // Método para exportar datos estadísticos
    public function exportarEstadisticas()
    {
        if (empty($this->resumenEstadisticas)) {
            session()->flash('error', 'Primero debe generar el mapa de calor para exportar las estadísticas.');
            return;
        }

        $datos = [
            'periodo' => [
                'desde' => $this->filtro_fecha_desde,
                'hasta' => $this->filtro_fecha_hasta
            ],
            'filtros_aplicados' => [
                'area' => $this->filtro_area ? $this->areas->find($this->filtro_area)?->nombre : 'Todas',
                'categoria' => $this->filtro_categoria ? $this->categorias->find($this->filtro_categoria)?->nombre : 'Todas',
                'barrio' => $this->filtro_barrio ? $this->barrios->find($this->filtro_barrio)?->nombre : 'Todos',
            ],
            'resumen' => $this->resumenEstadisticas,
            'coordenadas_reclamos' => $this->reclamosCoords,
            'fecha_exportacion' => now()->format('d/m/Y H:i:s')
        ];

        $nombreArchivo = 'estadisticas_reclamos_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->streamDownload(function () use ($datos) {
            echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $nombreArchivo, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"'
        ]);
    }

    // Métodos para actualizar filtros de forma reactiva
    public function updatedFiltroFechaDesde()
    {
        if ($this->filtro_fecha_desde && $this->filtro_fecha_hasta) {
            $this->generarMapaCalor(false);
        }
    }

    public function updatedFiltroFechaHasta()
    {
        if ($this->filtro_fecha_desde && $this->filtro_fecha_hasta) {
            $this->generarMapaCalor(false);
        }
    }

    public function updatedFiltroArea()
    {   
        $this->filtro_categoria = '';
        if($this->filtro_area) {
            $this->categorias = Categoria::where('area_id', $this->filtro_area)->orderBy('nombre')->get();
        }else {
            $this->categorias = Categoria::whereIn('area_id', $this->userAreas)->where('privada', false)->orderBy('nombre')->get();
        }
        $this->generarMapaCalor(false);
    }

    public function updatedFiltroCategoria()
    {
        $this->generarMapaCalor(false);
    }

    public function updatedFiltroBarrio()
    {
        $this->generarMapaCalor(false);
    }

    // Método para obtener datos de comparación temporal
    public function obtenerComparacionTemporal(): array
    {
        $fechaInicio = \Carbon\Carbon::parse($this->filtro_fecha_desde);
        $fechaFin = \Carbon\Carbon::parse($this->filtro_fecha_hasta);
        $diasPeriodo = $fechaInicio->diffInDays($fechaFin) + 1;
        
        // Período anterior del mismo tamaño
        $fechaInicioAnterior = $fechaInicio->copy()->subDays($diasPeriodo);
        $fechaFinAnterior = $fechaInicio->copy()->subDay();

        $estadisticasActuales = $this->estadisticasRendimiento(
            $this->userAreas, 
            $this->filtro_fecha_desde, 
            $this->filtro_fecha_hasta,
            $this->filtro_categoria,
            $this->filtro_area,
            $this->filtro_barrio
        );

        $estadisticasAnteriores = $this->estadisticasRendimiento(
            $this->userAreas, 
            $fechaInicioAnterior->format('Y-m-d'), 
            $fechaFinAnterior->format('Y-m-d'),
            $this->filtro_categoria,
            $this->filtro_area,
            $this->filtro_barrio
        );

        return [
            'actual' => $estadisticasActuales,
            'anterior' => $estadisticasAnteriores,
            'comparacion' => [
                'cambio_total' => $estadisticasActuales['total_reclamos'] - $estadisticasAnteriores['total_reclamos'],
                'cambio_porcentual' => $estadisticasAnteriores['total_reclamos'] > 0 
                    ? round((($estadisticasActuales['total_reclamos'] - $estadisticasAnteriores['total_reclamos']) / $estadisticasAnteriores['total_reclamos']) * 100, 1)
                    : 0,
                'tendencia' => $estadisticasActuales['total_reclamos'] > $estadisticasAnteriores['total_reclamos'] ? 'up' : 'down'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.estadisticas');
    }
}