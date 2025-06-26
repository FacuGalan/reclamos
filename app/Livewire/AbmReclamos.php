<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Estado;
use Illuminate\Support\Facades\Auth;

class AbmReclamos extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $busqueda_id = '';
    public $filtro_estado = '';
    public $filtro_area = '';
    public $filtro_categoria = '';
    public $filtro_fecha_desde = '';
    public $filtro_fecha_hasta = '';
    
    // Propiedades para navegación entre vistas
    public $currentView = 'list'; // 'list', 'create', 'edit'
    public $selectedReclamoId = null;
    public $showDeleteModal = false;
    public $selectedReclamo = null;
    public $reclamoInterno = false; // Para saber si es un reclamo interno o externo
    
    // Datos para los selects
    public $estados = [];
    public $areas = [];
    public $categorias = [];

    // ← ESTA ES LA CLAVE: agregar currentView y selectedReclamoId al queryString
    protected $queryString = [
        'busqueda' => ['except' => ''],
        'busqueda_id' => ['except' => ''],
        'filtro_estado' => ['except' => ''],
        'filtro_area' => ['except' => ''],
        'filtro_categoria' => ['except' => ''],
        'filtro_fecha_desde' => ['except' => ''],
        'filtro_fecha_hasta' => ['except' => ''],
        'currentView' => ['except' => 'list'],              // ← AGREGAR ESTO
        'selectedReclamoId' => ['except' => null, 'as' => 'reclamo'], // ← AGREGAR ESTO
    ];

    protected $listeners = [
        'reclamo-saved' => 'volverALista',
        'reclamo-deleted' => 'volverALista',
        'reclamo-actualizado' => 'volverAListaConDelay',
    ];

    public function mount()
    {
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::orderBy('nombre')->get();
        $this->categorias = Categoria::orderBy('nombre')->get();

        // ← AGREGAR VALIDACIÓN: Si está en modo edit, verificar que el reclamo existe
        if ($this->currentView === 'edit' && $this->selectedReclamoId) {
            $reclamo = Reclamo::find($this->selectedReclamoId);
            if (!$reclamo) {
                // Si el reclamo no existe, volver a la lista
                $this->currentView = 'list';
                $this->selectedReclamoId = null;
                session()->flash('error', 'El reclamo solicitado no existe.');
            }
        }
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingFiltroArea()
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria()
    {
        $this->resetPage();
    }

    public function getReclamos()
    {
        $query = Reclamo::with(['persona', 'categoria', 'area', 'estado', 'usuario', 'responsable'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
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

        // Aplicar filtro id
        if ($this->busqueda_id) {
            $query->where('id', $this->busqueda_id);
        }

        if ($this->filtro_estado) {
            $query->where('estado_id', $this->filtro_estado);
        }

        if ($this->filtro_area) {
            $query->where('area_id', $this->filtro_area);
        }

        if ($this->filtro_categoria) {
            $query->where('categoria_id', $this->filtro_categoria);
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
        $this->busqueda_id = '';
        $this->filtro_estado = '';
        $this->filtro_area = '';
        $this->filtro_categoria = '';
        $this->filtro_fecha_desde = '';
        $this->filtro_fecha_hasta = '';
        $this->resetPage();
    }

    // Navegación entre vistas
    public function nuevoReclamo()
    {
        $this->currentView = 'create';
        $this->selectedReclamoId = null;
        $this->reclamoInterno = false; // Indicar que es un reclamo externo
    }

    public function nuevoReclamoInterno()
    {
        $this->currentView = 'create';
        $this->selectedReclamoId = null;
        $this->reclamoInterno = true; // Indicar que es un reclamo interno
    }

    public function editarReclamo($reclamoId)
    {
        // Validar que el reclamo existe antes de cambiar
        $reclamo = Reclamo::find($reclamoId);
        if (!$reclamo) {
            session()->flash('error', 'El reclamo solicitado no existe.');
            return;
        }

        $this->selectedReclamoId = $reclamoId;
        $this->currentView = 'edit';
    }

    public function volverALista()
    {
        $this->currentView = 'list';
        $this->selectedReclamoId = null;
        $this->showDeleteModal = false;
        $this->selectedReclamo = null;
    }

    public function volverAListaConDelay()
    {
        // Esperar un poco para mostrar el mensaje de éxito y luego volver
        $this->dispatch('delay-return-to-list');
    }

    public function verReclamo($reclamoId)
    {
        $this->selectedReclamo = Reclamo::with(['persona', 'categoria', 'area', 'estado', 'usuario', 'responsable', 'domicilio', 'movimientos.tipoMovimiento', 'movimientos.estado', 'movimientos.usuario'])
            ->find($reclamoId);
        // Aquí podrías abrir un modal de detalle o redirigir a una vista específica
        $this->dispatch('mostrar-detalle-reclamo', ['reclamo' => $this->selectedReclamo]);
    }

    public function confirmarEliminacion($reclamoId)
    {
        $this->selectedReclamo = Reclamo::find($reclamoId);
        $this->showDeleteModal = true;
    }

    public function eliminarReclamo()
    {
        if ($this->selectedReclamo) {
            $this->selectedReclamo->delete();
            $this->showDeleteModal = false;
            $this->selectedReclamo = null;
            
            session()->flash('success', 'Reclamo eliminado exitosamente.');
            $this->dispatch('reclamo-deleted');
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedReclamo = null;
    }

    public function render()
    {
        // Solo obtener reclamos si estamos en la vista de lista
        $reclamos = $this->currentView === 'list' ? $this->getReclamos() : collect();
        
        return view('livewire.abm-reclamos', [
            'reclamos' => $reclamos
        ]);
    }
}