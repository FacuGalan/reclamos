<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cuadrilla;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class AbmCuadrillas extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $filtro_area = '';
    public $listaTimestamp = null;

    // Modal
    public $selectedCuadrillaId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedCuadrilla = null;

    // Datos del usuario
    public $areas = [];
    public $userAreas = [];

    // Formulario
    public $nombre = '';
    public $area_id = '';

    // Guardado y notificaciones
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filtro_area' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'nombre' => $this->isEditing
                ? 'required|string|max:255|unique:cuadrillas,nombre,' . $this->selectedCuadrillaId
                : 'required|string|max:255|unique:cuadrillas,nombre',
            'area_id' => 'required|exists:areas,id',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El campo Nombre es obligatorio',
        'nombre.unique' => 'Ya existe una cuadrilla con este nombre',
        'area_id.required' => 'Debe seleccionar un área',
        'area_id.exists' => 'El área seleccionada no es válida',
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['nombre', 'area_id'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function mount()
    {
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->listaTimestamp = microtime(true);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroArea()
    {
        $this->resetPage();
    }

    public function getCuadrillas()
    {
        $query = Cuadrilla::with('area')->whereIn('area_id', $this->userAreas)->orderBy('id');

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->filtro_area && in_array($this->filtro_area, $this->userAreas)) {
            $query->where('area_id', $this->filtro_area);
        }

        $this->listaTimestamp = microtime(true);

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->filtro_area = '';
        $this->resetPage();
    }

    // Modales
    public function nuevaCuadrilla()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedCuadrillaId = null;
        $this->showFormModal = true;

        if (count($this->userAreas) === 1) {
            $this->area_id = $this->userAreas[0];
        } else {
            $this->area_id = '';
        }
    }

    public function editarCuadrilla($id)
    {
        $cuadrilla = Cuadrilla::whereIn('area_id', $this->userAreas)->find($id);
        if (!$cuadrilla) {
            $this->mostrarNotificacionError('La cuadrilla no existe o no tienes permisos.');
            return;
        }

        $this->selectedCuadrillaId = $id;
        $this->isEditing = true;
        $this->cargarDatosCuadrilla($cuadrilla);
        $this->showFormModal = true;
    }

    public function cargarDatosCuadrilla($cuadrilla)
    {
        $this->nombre = $cuadrilla->nombre;
        $this->area_id = $cuadrilla->area_id;
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->area_id = '';
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedCuadrillaId = null;
        $this->isEditing = false;
    }

    public function confirmarEliminacion($id)
    {
        $cuadrilla = Cuadrilla::whereIn('area_id', $this->userAreas)->find($id);
        if (!$cuadrilla) {
            $this->mostrarNotificacionError('No tienes permisos para eliminar esta cuadrilla.');
            return;
        }

        $this->selectedCuadrilla = $cuadrilla;
        $this->showDeleteModal = true;
    }

    public function eliminar()
    {
        if ($this->selectedCuadrilla) {
            try {
                if (in_array($this->selectedCuadrilla->area_id, $this->userAreas)) {
                    $this->selectedCuadrilla->delete();
                    $this->showDeleteModal = false;
                    $this->selectedCuadrilla = null;
                    $this->mostrarNotificacionExito('Cuadrilla eliminada exitosamente');
                } else {
                    $this->mostrarNotificacionError('No tienes permisos para eliminar esta cuadrilla.');
                }
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar la cuadrilla: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedCuadrilla = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            $this->validate();

            if (!in_array($this->area_id, $this->userAreas)) {
                $this->mostrarNotificacionError('No tienes permisos para guardar en esta área.');
                $this->isSaving = false;
                return;
            }

            if (!$this->isEditing) {
                Cuadrilla::create([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id,
                ]);
                $mensaje = 'Cuadrilla creada exitosamente';
            } else {
                $cuadrilla = Cuadrilla::find($this->selectedCuadrillaId);
                if (!in_array($cuadrilla->area_id, $this->userAreas)) {
                    $this->mostrarNotificacionError('No tienes permisos para editar esta cuadrilla.');
                    $this->isSaving = false;
                    return;
                }
                $cuadrilla->update([
                    'nombre' => $this->nombre,
                    'area_id' => $this->area_id,
                ]);
                $mensaje = 'Cuadrilla actualizada exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSaving = false;
            throw $e;
        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar: ' . $e->getMessage());
        }

        $this->isSaving = false;
    }

    public function mostrarNotificacionExito($mensaje)
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
        return view('livewire.abm-cuadrillas', [
            'cuadrillas' => $this->getCuadrillas(),
            'areas' => $this->areas,
        ]);
    }
}
