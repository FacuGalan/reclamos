<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;

class AbmMotivos extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $filtro_area = '';
    public $listaTimestamp = null; // Para forzar la actualización de la lista
    
    // Propiedades para modales
    public $selectedMotivoId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedMotivo = null;
    
    // Datos para los selects
    public $areas = [];
    public $userAreas = []; // Áreas del usuario logueado

    // Datos del formulario
    public $nombre = '';
    public $area_id = '';
    public $privada = false;

    // Estado de guardado
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    public $ver_privada = false;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtro_area' => ['except' => ''],
    ];

    // SOLUCIÓN: Usar método rules() en lugar de propiedad estática
    protected function rules()
    {
        return [
            'nombre' => $this->isEditing 
                ? 'required|string|max:255|unique:categorias,nombre,' . $this->selectedMotivoId
                : 'required|string|max:255|unique:categorias,nombre',
            'area_id' => 'required|exists:areas,id'
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre del motivo es obligatorio',
        'nombre.unique' => 'Ya existe un motivo con este nombre',
        'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
        'area_id.required' => 'Debe seleccionar un área',
        'area_id.exists' => 'El área seleccionada no es válida',
    ];

    // NUEVO: Validación en tiempo real
    public function updated($propertyName)
    {
        // Validar cuando cambie el nombre, area_id o estado_id
        if (in_array($propertyName, ['nombre', 'area_id'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();
        $this->ver_privada = Auth::user()->ver_privada;

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        // Cargar datos filtrados por las áreas del usuario
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
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

    public function getMotivos()
    {
        $query = Categoria::with(['area'])
            ->whereIn('area_id', $this->userAreas) // Solo motivos de áreas del usuario
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

        // FILTRO POR MOTIVOS PRIVADOS

        if(Auth::user()->rol->id > 1){
            $query->where('privada', $this->ver_privada);
        }

        $this->listaTimestamp = microtime(true);

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_area = '';
        $this->resetPage();
    }

    // Métodos para modales
    public function nuevoMotivo()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedMotivoId = null;
        $this->showFormModal = true;
        if(count($this->userAreas) === 1) {
            $this->area_id = $this->userAreas[0]; // Asignar automáticamente el área si solo hay una
        } else {
            $this->area_id = ''; // Dejar vacío para que el usuario seleccione
        }
    }

    public function editarMotivo($motivoId)
    {
        $motivo = Categoria::whereIn('area_id', $this->userAreas)->find($motivoId);
        if (!$motivo) {
            $this->mostrarNotificacionError('El motivo solicitado no existe o no tienes permisos para acceder a él.');
            return;
        }

        $this->selectedMotivoId = $motivoId;
        $this->isEditing = true;
        $this->cargarDatosMotivo($motivo);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedMotivoId = null;
        $this->isEditing = false;
    }

    public function cargarDatosMotivo($motivo)
    {
        $this->nombre = $motivo->nombre;
        $this->area_id = $motivo->area_id;
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->area_id = '';
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($motivoId)
    {
        $motivo = Categoria::whereIn('area_id', $this->userAreas)->find($motivoId);
        if (!$motivo) {
            $this->mostrarNotificacionError('No tienes permisos para eliminar este motivo.');
            return;
        }
        
        $this->selectedMotivo = $motivo;
        $this->showDeleteModal = true;
    }

    public function eliminarMotivo()
    {
        if ($this->selectedMotivo) {
            try {
                // Verificar una vez más que el usuario tiene permisos
                if (in_array($this->selectedMotivo->area_id, $this->userAreas)) {
                    $this->selectedMotivo->delete();
                    $this->showDeleteModal = false;
                    $this->selectedMotivo = null;
                    
                    $this->mostrarNotificacionExito('Motivo eliminado exitosamente');
                } else {
                    $this->mostrarNotificacionError('No tienes permisos para eliminar este motivo.');
                    $this->showDeleteModal = false;
                    $this->selectedMotivo = null;
                }
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar el motivo: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedMotivo = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            // PASO 1: Validar campos PRIMERO
            $this->validate();

            // PASO 2: Verificar permisos DESPUÉS de la validación de campos
            if (!in_array($this->area_id, $this->userAreas)) {
                $this->mostrarNotificacionError('No tienes permisos para crear motivos en el área seleccionada.');
                $this->isSaving = false;
                return;
            }

            // PASO 3: Lógica de guardado
            if (!$this->isEditing) {
                Categoria::create([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id,
                    'privada' => $this->ver_privada ? 1 : 0,
                ]);
                
                $mensaje = 'Motivo creado exitosamente';
            } else {
                $motivo = Categoria::find($this->selectedMotivoId);
                
                // Verificar permisos antes de actualizar
                if (!in_array($motivo->area_id, $this->userAreas)) {
                    $this->mostrarNotificacionError('No tienes permisos para editar este motivo.');
                    $this->isSaving = false;
                    return;
                }
                
                $motivo->update([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id
                ]);
                
                $mensaje = 'Motivo actualizado exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // CRÍTICO: Re-lanzar la excepción para que se muestren los errores
            $this->isSaving = false;
            throw $e;
            
        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar el motivo: ' . $e->getMessage());
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
        $motivo = $this->getMotivos();
        
        return view('livewire.abm-motivos', [
            'motivos' => $motivo
        ]);
    }
}