<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Edificio;
use Illuminate\Support\Facades\Auth;

class AbmEdificios extends Component
{
    use WithPagination;


    // Filtros
    public $busqueda = '';
    public $filtro_area = '';

    // Modales
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $isEditing = false;
    public $selectedEdificioId = null;
    public $selectedEdificio = null;

    // Datos
    public $areas = [];
    public $userAreas = [];
    public $nombre = '';
    public $direccion = '';
    public $area_id = '';

    // Guardado
    public $isSaving = false;

    // Notificaciones
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtro_area' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'nombre' => $this->isEditing
                ? 'required|string|max:255|unique:edificios,nombre,' . $this->selectedEdificioId
                : 'required|string|max:255|unique:edificios,nombre',
            'direccion' => 'required|string|max:255',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'nombre.unique' => 'Ya existe un edificio con este nombre',
        'nombre.max' => 'El nombre no puede exceder 255 caracteres',
        'area_id.required' => 'Debe seleccionar un área',
        'area_id.exists' => 'El área seleccionada no es válida',
    ];

    public function mount()
    {
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
    }

    public function updatingBusqueda() { $this->resetPage(); }
    public function updatingFiltroArea() { $this->resetPage(); }

    public function getEdificios()
    {
        $query = Edificio::query()->orderBy('id');

        if ($this->busqueda) {
            $query->where('nombre', 'like', "%{$this->busqueda}%");
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_area = '';
        $this->resetPage();
    }

    public function nuevoEdificio()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showFormModal = true;
    }

    public function editarEdificio($id)
    {
        $edificio = Edificio::find($id);
        if (!$edificio) return $this->mostrarNotificacionError('No tiene permisos para editar este edificio.');

        $this->selectedEdificioId = $id;
        $this->isEditing = true;
        $this->nombre = $edificio->nombre;
        $this->direccion = $edificio->direccion;
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->direccion = '';
        $this->selectedEdificioId = null;
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($id)
    {
        $edificio = Edificio::whereIn('area_id', $this->userAreas)->find($id);
        if (!$edificio) return $this->mostrarNotificacionError('No tiene permisos para eliminar este edificio.');

        $this->selectedEdificio = $edificio;
        $this->showDeleteModal = true;
    }

    public function eliminarEdificio()
    {
        if ($this->selectedEdificio) {
            $this->selectedEdificio->delete();
            $this->showDeleteModal = false;
            $this->selectedEdificio = null;
            $this->mostrarNotificacionExito('Edificio eliminado exitosamente.');
        }
    }

    public function save()
    {
        $this->validate([
            'nombre' => $this->isEditing
                ? 'required|string|max:255|unique:edificios,nombre,' . $this->selectedEdificioId
                : 'required|string|max:255|unique:edificios,nombre',
            'direccion' => 'required|string|max:255',
        ]);

        if (!$this->isEditing) {
            // Crear edificio
            Edificio::create([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
            ]);
            $this->mostrarNotificacionExito('Edificio creado exitosamente');
        } else {
            // Actualizar edificio
            $edificio = Edificio::find($this->selectedEdificioId);
            $edificio->update([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
            ]);
            $this->mostrarNotificacionExito('Edificio actualizado exitosamente');
        }

        // Cerrar modal y resetear formulario
        $this->cerrarModal();
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
        return view('livewire.abm-edificios', [
            'edificios' => $this->getEdificios(),
        ]);
    }
}
