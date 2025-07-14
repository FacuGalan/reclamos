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
    public $reclamoEditable = true; // Para saber si se está editando un reclamo
    public $selectedReclamoId = null;
    public $showDeleteModal = false;
    public $selectedReclamo = null;
    public $reclamoInterno = false; // Para saber si es un reclamo interno o externo
    
    // Datos para los selects (filtrados por áreas del usuario)
    public $estados = [];
    public $areas = [];
    public $categorias = [];

    public $listaTimestamp; // NUEVO: para forzar re-renderización

    // Áreas del usuario logueado
    public $userAreas = [];

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
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        $this->listaTimestamp = microtime(true); // Inicializar timestamp

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        // Cargar datos filtrados por las áreas del usuario
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->categorias = Categoria::whereIn('area_id', $this->userAreas)->orderBy('nombre')->get();

        // Validación: Si está en modo edit, verificar que el reclamo existe y pertenece a las áreas del usuario
        if ($this->currentView === 'edit' && $this->selectedReclamoId) {
            $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($this->selectedReclamoId);
            if (!$reclamo) {
                // Si el reclamo no existe o no pertenece a las áreas del usuario, volver a la lista
                $this->currentView = 'list';
                $this->selectedReclamoId = null;
                session()->flash('error', 'El reclamo solicitado no existe o no tienes permisos para acceder a él.');
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
            ->whereIn('area_id', $this->userAreas) // ← FILTRO PRINCIPAL: Solo reclamos de áreas del usuario
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

        // Aplicar filtro id
        if ($this->busqueda_id) {
            $query->where('id', $this->busqueda_id);
        }

        if ($this->filtro_estado) {
            $query->where('estado_id', $this->filtro_estado);
        }else{
            $query->whereNot('estado_id', 5)->whereNot('estado_id', 4); // Excluir estados "Cancelado" y "Finalizado"
        }

        if ($this->filtro_area) {
            // Verificar que el área filtrada esté dentro de las áreas permitidas del usuario
            if (in_array($this->filtro_area, $this->userAreas)) {
                $query->where('area_id', $this->filtro_area);
            }
        }

        if ($this->filtro_categoria) {
            // Verificar que la categoría pertenezca a las áreas permitidas del usuario
            $categoria = Categoria::whereIn('area_id', $this->userAreas)->find($this->filtro_categoria);
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

        $this->listaTimestamp = microtime(true);

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

    public function editarReclamo($reclamoId,$edita)
    {
        // Validar que el reclamo existe y pertenece a las áreas del usuario
        $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($reclamoId);
        if (!$reclamo) {
            session()->flash('error', 'El reclamo solicitado no existe o no tienes permisos para acceder a él.');
            return;
        }

        $this->selectedReclamoId = $reclamoId;
        $this->reclamoEditable = $edita;
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
        // Verificar permisos antes de mostrar el detalle
        $reclamo = Reclamo::with(['persona', 'categoria', 'area', 'estado', 'usuario', 'responsable', 'domicilio', 'movimientos.tipoMovimiento', 'movimientos.estado', 'movimientos.usuario'])
            ->whereIn('area_id', $this->userAreas)
            ->find($reclamoId);
            
        if (!$reclamo) {
            session()->flash('error', 'No tienes permisos para ver este reclamo.');
            return;
        }
        
        $this->selectedReclamo = $reclamo;
        $this->dispatch('mostrar-detalle-reclamo', ['reclamo' => $this->selectedReclamo]);
    }

    public function confirmarEliminacion($reclamoId)
    {
        // Verificar permisos antes de eliminar
        $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($reclamoId);
        if (!$reclamo) {
            session()->flash('error', 'No tienes permisos para eliminar este reclamo.');
            return;
        }
        
        $this->selectedReclamo = $reclamo;
        $this->showDeleteModal = true;
    }

    public function eliminarReclamo()
    {
        if ($this->selectedReclamo) {
            // Verificar una vez más que el usuario tiene permisos
            if (in_array($this->selectedReclamo->area_id, $this->userAreas)) {
                //$this->selectedReclamo->delete();
                $this->selectedReclamo->update([
                    'estado_id' => 5, // Asumiendo que el estado 5 es "Cancelado"
                    'responsable_id' => Auth::id(), // Asignar el usuario que elimina
                ]);
                $this->showDeleteModal = false;
                $this->selectedReclamo = null;

                $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');
                
                session()->flash('success', 'Reclamo eliminado exitosamente.');
                $this->dispatch('reclamo-deleted');
            } else {
                session()->flash('error', 'No tienes permisos para eliminar este reclamo.');
                $this->showDeleteModal = false;
                $this->selectedReclamo = null;
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedReclamo = null;
    }

    // Método para obtener información de áreas del usuario (útil para debugging)
    public function getUserAreasInfo()
    {
        return [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'areas_count' => count($this->userAreas),
            'areas_names' => Area::whereIn('id', $this->userAreas)->pluck('nombre')->toArray(),
        ];
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