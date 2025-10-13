<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Reporte;
use App\Models\ReporteCategoria;

class AbmReportes extends Component
{
    use WithPagination;

     // Propiedades para filtros
    public $busqueda = '';
    public $filtro_categoria = '';
    public $filtro_fecha_desde = '';
    public $filtro_fecha_hasta = '';

      // Propiedades para el modal
    public $mostrarModal = false;
    public $reporteSeleccionado = null;
    
    // Datos para los selects (filtrados por áreas del usuario)
    public $categorias = [];

     public function mount()
    {
        // Cargar datos filtrados
        $this->categorias = ReporteCategoria::orderBy('nombre')->get();
    }

    // Métodos para el modal
    public function abrirModal($reporteId)
{
    \Log::info('=== DIRECTO - abrirModal ID: ' . $reporteId);
    
    $this->reporteSeleccionado = Reporte::with(['persona', 'categoria', 'domicilio'])->find($reporteId);
    
    \Log::info('=== DIRECTO - Reporte encontrado: ', [
        'id' => $this->reporteSeleccionado ? $this->reporteSeleccionado->id : 'NULL',
        'observaciones' => $this->reporteSeleccionado ? $this->reporteSeleccionado->observaciones : 'NULL',
        'tiene_domicilio' => $this->reporteSeleccionado && $this->reporteSeleccionado->domicilio ? 'SI' : 'NO'
    ]);
    
    $this->mostrarModal = true;
    
    // MODIFICADO: Pasar los parámetros directamente, no en un array
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

     public function getReporteProperty()
    {
      \Log::info('=== getReporteProperty EJECUTADO ===', [
        'reporteId_actual' => $this->reporteId,
        'timestamp' => now()->format('H:i:s.u')
    ]);
    
    if ($this->reporteId) {
        $reporte = Reporte::with(['persona', 'categoria', 'domicilio'])->find($this->reporteId);
        
        \Log::info('=== REPORTE CONSULTADO EN DB ===', [
            'id_buscado' => $this->reporteId,
            'id_encontrado' => $reporte ? $reporte->id : 'NULL',
            'observaciones' => $reporte ? $reporte->observaciones : 'NULL'
        ]);
        
        return $reporte;
    }
    
    \Log::info('=== getReporteProperty - No hay reporteId ===');
    return null;
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

    public function getReportes()
    {
        $query = Reporte::with(['persona', 'categoria','domicilio'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros adicionales
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('direccion', 'like', '%' . $this->busqueda . '%')
                  ->orWhereHas('persona', function($subQ) {
                      $subQ->where('nombre', 'like', '%' . $this->busqueda . '%')
                           ->orWhere('apellido', 'like', '%' . $this->busqueda . '%')
                           ->orWhere('dni', 'like', '%' . $this->busqueda . '%');
                  });
            });
        }

        if ($this->filtro_categoria) {
            // Verificar que la categoría pertenezca a las áreas permitidas del usuario
            $categoria = ReporteCategoria::find($this->filtro_categoria);
            if ($categoria) {
                $query->where('categoria_id', $this->filtro_categoria);
            }
        }

        if ($this->filtro_fecha_desde) {
            $query->where('fecha', '>=', $this->filtro_fecha_desde);
        }

        if ($this->filtro_fecha_hasta) {
            $query->where('fecha', '<=', $this->filtro_fecha_hasta);
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_categoria = '';
        $this->filtro_fecha_desde = '';
        $this->filtro_fecha_hasta = '';
        $this->resetPage();
    }

    public function render()
    {
       // Solo obtener reclamos si estamos en la vista de lista
        $reportes = $this->getReportes();
        
        return view('livewire.abm-reportes', [
            'reportes' => $reportes
        ]);
    }
}
