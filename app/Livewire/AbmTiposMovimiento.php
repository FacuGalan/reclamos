<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TipoMovimiento;
use App\Models\Area;
use App\Models\Estado;
use Illuminate\Support\Facades\Auth;

class AbmTiposMovimiento extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $filtro_area = '';
    public $filtro_estado = '';
    public $listaTimestamp = null; // Para forzar la actualización de la lista
    
    // Propiedades para modales
    public $selectedTipoMovimientoId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedTipoMovimiento = null;
    
    // Datos para los selects
    public $areas = [];
    public $estados = [];
    public $userAreas = []; // Áreas del usuario logueado

    // Datos del formulario
    public $nombre = '';
    public $area_id = '';
    public $estado_id = '';

    // Estado de guardado
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtro_area' => ['except' => ''],
        'filtro_estado' => ['except' => ''],
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:tipo_movimientos,nombre',
        'area_id' => 'required|exists:areas,id',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del tipo de movimiento es obligatorio',
        'nombre.unique' => 'Ya existe un tipo de movimiento con este nombre',
        'area_id.required' => 'Debe seleccionar un área',
        'area_id.exists' => 'El área seleccionada no es válida',
    ];

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        // Cargar datos filtrados por las áreas del usuario
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->estados = Estado::orderBy('nombre')->get();

        $this->listaTimestamp = microtime(true);
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroArea()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function getTiposMovimiento()
    {
        $query = TipoMovimiento::with(['area', 'estado'])
            ->whereIn('area_id', $this->userAreas) // Solo tipos de movimiento de áreas del usuario
            ->orderBy('id');

        // Aplicar filtros
        if ($this->busqueda) {
            $query->where('nombre', 'like', '%' . $this->busqueda . '%');
        }

        if ($this->filtro_area) {
            // Verificar que el área filtrada esté dentro de las áreas permitidas del usuario
            if (in_array($this->filtro_area, $this->userAreas)) {
                $query->where('area_id', $this->filtro_area);
            }
        }

        if ($this->filtro_estado) {
            $query->where('estado_id', $this->filtro_estado);
        }

        $this->listaTimestamp = microtime(true);

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_area = '';
        $this->filtro_estado = '';
        $this->resetPage();
    }

    // Métodos para modales
    public function nuevoTipoMovimiento()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedTipoMovimientoId = null;
        $this->showFormModal = true;
    }

    public function editarTipoMovimiento($tipoMovimientoId)
    {
        $tipoMovimiento = TipoMovimiento::whereIn('area_id', $this->userAreas)->find($tipoMovimientoId);
        if (!$tipoMovimiento) {
            $this->mostrarNotificacionError('El tipo de movimiento solicitado no existe o no tienes permisos para acceder a él.');
            return;
        }

        $this->selectedTipoMovimientoId = $tipoMovimientoId;
        $this->isEditing = true;
        $this->cargarDatosTipoMovimiento($tipoMovimiento);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedTipoMovimientoId = null;
        $this->isEditing = false;
    }

    public function cargarDatosTipoMovimiento($tipoMovimiento)
    {
        $this->nombre = $tipoMovimiento->nombre;
        $this->area_id = $tipoMovimiento->area_id;
        $this->estado_id = $tipoMovimiento->estado_id;
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->area_id = '';
        $this->estado_id = '';
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($tipoMovimientoId)
    {
        $tipoMovimiento = TipoMovimiento::whereIn('area_id', $this->userAreas)->find($tipoMovimientoId);
        if (!$tipoMovimiento) {
            $this->mostrarNotificacionError('No tienes permisos para eliminar este tipo de movimiento.');
            return;
        }
        
        $this->selectedTipoMovimiento = $tipoMovimiento;
        $this->showDeleteModal = true;
    }

    public function eliminarTipoMovimiento()
    {
        if ($this->selectedTipoMovimiento) {
            try {
                // Verificar una vez más que el usuario tiene permisos
                if (in_array($this->selectedTipoMovimiento->area_id, $this->userAreas)) {
                    $this->selectedTipoMovimiento->delete();
                    $this->showDeleteModal = false;
                    $this->selectedTipoMovimiento = null;
                    
                    $this->mostrarNotificacionExito('Tipo de movimiento eliminado exitosamente');
                } else {
                    $this->mostrarNotificacionError('No tienes permisos para eliminar este tipo de movimiento.');
                    $this->showDeleteModal = false;
                    $this->selectedTipoMovimiento = null;
                }
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar el tipo de movimiento: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedTipoMovimiento = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            // Verificar que el área seleccionada esté dentro de las áreas permitidas del usuario
            if (!in_array($this->area_id, $this->userAreas)) {
                $this->mostrarNotificacionError('No tienes permisos para crear tipos de movimiento en el área seleccionada.');
                $this->isSaving = false;
                return;
            }

            if ($this->isEditing) {
                // Actualizar reglas para edición
                $this->rules['nombre'] = 'required|string|max:255|unique:tipo_movimientos,nombre,' . $this->selectedTipoMovimientoId;
            }

            $this->validate();

            if (!$this->isEditing) {
                TipoMovimiento::create([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id,
                    'estado_id' => 2 //$this->estado_id,
                ]);
                
                $mensaje = 'Tipo de movimiento creado exitosamente';
            } else {
                $tipoMovimiento = TipoMovimiento::find($this->selectedTipoMovimientoId);
                
                // Verificar permisos antes de actualizar
                if (!in_array($tipoMovimiento->area_id, $this->userAreas)) {
                    $this->mostrarNotificacionError('No tienes permisos para editar este tipo de movimiento.');
                    $this->isSaving = false;
                    return;
                }
                
                $tipoMovimiento->update([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id,
                    'estado_id' => 2 //$this->estado_id,
                ]);
                
                $mensaje = 'Tipo de movimiento actualizado exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();

        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar el tipo de movimiento: ' . $e->getMessage());
        }

        $this->isSaving = false;
    }

    public function mostrarNotificacionExito($mensaje = 'Operación realizada exitosamente')
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'success';
        $this->notificacionTimestamp = microtime(true);
    }

    public function mostrarNotificacionError($mensaje)
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'error';
        $this->notificacionTimestamp = microtime(true);
    }

    public function render()
    {
        $tiposMovimiento = $this->getTiposMovimiento();
        
        return view('livewire.abm-tipos-movimiento', [
            'tiposMovimiento' => $tiposMovimiento
        ]);
    }
}