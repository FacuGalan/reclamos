<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Reporte;
use App\Models\ReporteCategoria;
use App\Models\ReporteEstado;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Exports\GenericExport;

class AbmReportes extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $filtro_categoria = '';
    public $filtro_estado = '';
    public $filtro_fecha_desde = '';
    public $filtro_fecha_hasta = '';
    public $mostrar_finalizados = false; // Nueva propiedad para el toggle

    // Propiedades para el modal de visualización
    public $mostrarModal = false;
    public $reporteSeleccionado = null;
    
    // Propiedades para el modal de cambio de estado
    public $mostrarModalEstado = false;
    public $reporteParaCambioEstado = null;
    public $nuevoEstadoId = '';
    public $observacionesCambioEstado = '';
    
    // Datos para los selects
    public $categorias = [];
    public $estados = [];

    public function mount()
    {
        // Cargar datos
        $this->categorias = ReporteCategoria::orderBy('nombre')->get();
        $this->cargarEstados();
    }

    public function cargarEstados()
    {
        // Cargar estados según el toggle de finalizados
        if ($this->mostrar_finalizados) {
            $this->estados = ReporteEstado::orderBy('id')->get();
        } else {
            $this->estados = ReporteEstado::noFinalizados()->orderBy('id')->get();
        }
    }

    // Métodos para el modal de visualización
    public function abrirModal($reporteId)
    {
        \Log::info('=== DIRECTO - abrirModal ID: ' . $reporteId);
        
        $this->reporteSeleccionado = Reporte::with(['persona', 'categoria', 'domicilio', 'estado'])->find($reporteId);
        
        \Log::info('=== DIRECTO - Reporte encontrado: ', [
            'id' => $this->reporteSeleccionado ? $this->reporteSeleccionado->id : 'NULL',
            'observaciones' => $this->reporteSeleccionado ? $this->reporteSeleccionado->observaciones : 'NULL',
            'tiene_domicilio' => $this->reporteSeleccionado && $this->reporteSeleccionado->domicilio ? 'SI' : 'NO'
        ]);
        
        $this->mostrarModal = true;
        
        if ($this->reporteSeleccionado && $this->reporteSeleccionado->domicilio && $this->reporteSeleccionado->domicilio->coordenadas) {
            $this->dispatch('inicializar-mapa-reporte', 
                reporteId: $this->reporteSeleccionado->id,
                coordenadas: $this->reporteSeleccionado->domicilio->coordenadas
            );
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->reporteSeleccionado = null;
    }

    // Métodos para el modal de cambio de estado
    public function abrirModalCambioEstado($reporteId)
    {
        $this->reporteParaCambioEstado = Reporte::with(['estado'])->find($reporteId);
        $this->nuevoEstadoId = '';
        $this->observacionesCambioEstado = '';
        $this->mostrarModalEstado = true;
    }

    public function cambiarEstado()
    {
        $this->validate([
            'nuevoEstadoId' => 'required|exists:reporte_estados,id',
        ], [
            'nuevoEstadoId.required' => 'Debe seleccionar un estado',
            'nuevoEstadoId.exists' => 'El estado seleccionado no es válido',
        ]);

        try {
            $this->reporteParaCambioEstado->cambiarEstado(
                $this->nuevoEstadoId,
                $this->observacionesCambioEstado
            );

            $this->cerrarModalEstado();
            
            session()->flash('success', 'Estado cambiado exitosamente');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function cerrarModalEstado()
    {
        $this->mostrarModalEstado = false;
        $this->reporteParaCambioEstado = null;
        $this->nuevoEstadoId = '';
        $this->observacionesCambioEstado = '';
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingMostrarFinalizados()
    {
        $this->cargarEstados();
        $this->filtro_estado = ''; // Resetear el filtro de estado al cambiar el toggle
        $this->resetPage();
    }

    public function getReportes($forExport = false)
    {
        $query = Reporte::with(['persona', 'categoria', 'domicilio', 'estado'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros adicionales
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('observaciones', 'like', '%' . $this->busqueda . '%')
                ->orWhereHas('domicilio', function($subQ) {
                    $subQ->where('direccion', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('entre_calles', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('direccion_rural', 'like', '%' . $this->busqueda . '%');
                })
                ->orWhereHas('persona', function($subQ) {
                    $subQ->where('nombre', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('apellido', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('dni', 'like', '%' . $this->busqueda . '%');
                });
            });
        }

        if ($this->filtro_categoria) {
            $categoria = ReporteCategoria::find($this->filtro_categoria);
            if ($categoria) {
                $query->where('categoria_id', $this->filtro_categoria);
            }
        }

        if ($this->filtro_estado) {
            $query->where('estado_id', $this->filtro_estado);
        }else{
            // Filtrar por estados no finalizados por defecto
            if (!$this->mostrar_finalizados) {
                $query->noFinalizados();
            }
        }

        if ($this->filtro_fecha_desde) {
            $query->where('fecha', '>=', $this->filtro_fecha_desde);
        }

        if ($this->filtro_fecha_hasta) {
            $query->where('fecha', '<=', $this->filtro_fecha_hasta);
        }

        if ($forExport) {
            return $query;
        } else {
            return $query->paginate(15);
        }
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_categoria = '';
        $this->filtro_estado = '';
        $this->filtro_fecha_desde = '';
        $this->filtro_fecha_hasta = '';
        $this->mostrar_finalizados = false;
        $this->cargarEstados();
        $this->resetPage();
    }

    public function exportarExcel()
    {
        $data = $this->getReportes(true)->get();
        
        $headings = [
            'ID',
            'Fecha incidente',
            'Nombre',
            'Apellido',
            'DNI',
            'Teléfono',
            'Email',
            'Categoría',
            'Estado',
            'Dirección',
            'Entre calles',
            'Aclaración Dirección',
            'Barrio',
            'Fecha Creación',
            'Habitual',
            'Descripción'
        ];
        
        $mappingCallback = function ($reporte) {
            return [
                $reporte->id,
                \Carbon\Carbon::parse($reporte->fecha)->format('d/m/Y'),
                $reporte->persona->nombre ?? 'N/A',
                $reporte->persona->apellido ?? 'N/A',
                $reporte->persona->dni ?? 'N/A',
                $reporte->persona->telefono ?? 'N/A',
                $reporte->persona->email ?? 'N/A',
                $reporte->categoria->nombre ?? 'N/A',
                $reporte->estado->nombre ?? 'Sin estado',
                $reporte->domicilio->direccion ?? 'N/A',
                $reporte->domicilio->entre_calles ?? 'N/A',
                $reporte->domicilio->direccion_rural ?? 'N/A',
                $reporte->domicilio->barrio->nombre ?? 'N/A',
                $reporte->created_at->format('d/m/Y H:i'),
                $reporte->habitual ? 'Sí' : 'No',
                $reporte->observaciones,
            ];
        };
        
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '77BF43'],
            ],
        ];
        
        $export = new GenericExport(
            $data,
            $headings,
            $mappingCallback,
            'Reportes de seguridad - ' . date('d-m-Y'),
            $headerStyle
        );
        
        return $export->download();
    }

    public function render()
    {
        $reportes = $this->getReportes();
        
        return view('livewire.abm-reportes', [
            'reportes' => $reportes
        ]);
    }
}