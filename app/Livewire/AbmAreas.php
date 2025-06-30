<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Secretaria;
use Illuminate\Support\Facades\Auth;

class AbmAreas extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $filtro_secretaria = '';
    
    // Propiedades para modales
    public $selectedAreaId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedArea = null;
    
    // Datos para los selects
    public $secretarias = [];

    // Datos del formulario
    public $nombre = '';
    public $secretaria_id = '';

    // Estado de guardado
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtro_secretaria' => ['except' => ''],
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:areas,nombre',
        'secretaria_id' => 'required|exists:secretarias,id',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del área es obligatorio',
        'nombre.unique' => 'Ya existe un área con este nombre',
        'secretaria_id.required' => 'Debe seleccionar una secretaría',
        'secretaria_id.exists' => 'La secretaría seleccionada no es válida',
    ];

    public function mount()
    {
        $this->secretarias = Secretaria::orderBy('nombre')->get();
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroSecretaria()
    {
        $this->resetPage();
    }

    public function getAreas()
    {
        $query = Area::with(['secretaria'])
            ->orderBy('nombre');

        // Aplicar filtros
        if ($this->busqueda) {
            $query->where('nombre', 'like', '%' . $this->busqueda . '%');
        }

        if ($this->filtro_secretaria) {
            $query->where('secretaria_id', $this->filtro_secretaria);
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_secretaria = '';
        $this->resetPage();
    }

    // Métodos para modales
    public function nuevaArea()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedAreaId = null;
        $this->showFormModal = true;
    }

    public function editarArea($areaId)
    {
        $area = Area::find($areaId);
        if (!$area) {
            $this->mostrarNotificacionError('El área solicitada no existe.');
            return;
        }

        $this->selectedAreaId = $areaId;
        $this->isEditing = true;
        $this->cargarDatosArea($area);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedAreaId = null;
        $this->isEditing = false;
    }

    public function cargarDatosArea($area)
    {
        $this->nombre = $area->nombre;
        $this->secretaria_id = $area->secretaria_id;
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->secretaria_id = '';
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($areaId)
    {
        $this->selectedArea = Area::find($areaId);
        $this->showDeleteModal = true;
    }

    public function eliminarArea()
    {
        if ($this->selectedArea) {
            try {
                $this->selectedArea->delete();
                $this->showDeleteModal = false;
                $this->selectedArea = null;
                
                $this->mostrarNotificacionExito('Área eliminada exitosamente');
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar el área: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedArea = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            if ($this->isEditing) {
                // Actualizar reglas para edición
                $this->rules['nombre'] = 'required|string|max:255|unique:areas,nombre,' . $this->selectedAreaId;
            }

            $this->validate();

            if (!$this->isEditing) {
                Area::create([
                    'nombre' => $this->nombre,
                    'secretaria_id' => $this->secretaria_id,
                ]);
                
                $mensaje = 'Área creada exitosamente';
            } else {
                $area = Area::find($this->selectedAreaId);
                $area->update([
                    'nombre' => $this->nombre,
                    'secretaria_id' => $this->secretaria_id,
                ]);
                
                $mensaje = 'Área actualizada exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();

        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar el área: ' . $e->getMessage());
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
        $areas = $this->getAreas();
        
        return view('livewire.abm-areas', [
            'areas' => $areas
        ]);
    }
}