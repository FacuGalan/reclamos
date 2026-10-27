<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContactoAgenda;
use Illuminate\Support\Facades\Auth;

class AbmAgenda extends Component
{
    use WithPagination;

    // Filtros
    public $busqueda = '';

    // Modales
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $isEditing = false;
    public $selectedContactoId = null;
    public $selectedContacto = null;

    // Datos del formulario
    public $nombre = '';
    public $telefono = '';
    public $ocupacion = '';

    // Notificaciones
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'nombre.max' => 'El nombre no puede exceder 255 caracteres',
        'telefono.max' => 'El teléfono no puede exceder 50 caracteres',
        'ocupacion.max' => 'La ocupación no puede exceder 100 caracteres',
    ];

    public function mount()
    {
        // La agenda solo se usa para reclamos internos
        abort_unless(Auth::user()->ver_privada, 403);
    }

    public function updatingBusqueda() { $this->resetPage(); }

    public function getContactos()
    {
        $query = ContactoAgenda::query()->orderBy('nombre');

        if ($this->busqueda) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('ocupacion', 'like', "%{$this->busqueda}%")
                  ->orWhere('telefono', 'like', "%{$this->busqueda}%");
            });
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->resetPage();
    }

    public function nuevoContacto()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showFormModal = true;
    }

    public function editarContacto($id)
    {
        $contacto = ContactoAgenda::find($id);
        if (!$contacto) return $this->mostrarNotificacionError('El contacto no existe.');

        $this->selectedContactoId = $id;
        $this->isEditing = true;
        $this->nombre = $contacto->nombre;
        $this->telefono = $contacto->telefono;
        $this->ocupacion = $contacto->ocupacion;
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
        $this->telefono = '';
        $this->ocupacion = '';
        $this->selectedContactoId = null;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($id)
    {
        $contacto = ContactoAgenda::find($id);
        if (!$contacto) return $this->mostrarNotificacionError('El contacto no existe.');

        $this->selectedContacto = $contacto;
        $this->showDeleteModal = true;
    }

    public function eliminarContacto()
    {
        if ($this->selectedContacto) {
            // Soft delete: los reclamos que ya lo tenían asignado lo siguen mostrando
            $this->selectedContacto->delete();
            $this->showDeleteModal = false;
            $this->selectedContacto = null;
            $this->mostrarNotificacionExito('Contacto eliminado de la agenda.');
        }
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'ocupacion' => 'nullable|string|max:100',
        ]);

        $datos = [
            'nombre' => trim($this->nombre),
            'telefono' => trim($this->telefono) ?: null,
            'ocupacion' => trim($this->ocupacion) ?: null,
        ];

        if (!$this->isEditing) {
            ContactoAgenda::create($datos);
            $this->mostrarNotificacionExito('Contacto creado exitosamente');
        } else {
            ContactoAgenda::findOrFail($this->selectedContactoId)->update($datos);
            $this->mostrarNotificacionExito('Contacto actualizado exitosamente');
        }

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
        return view('livewire.abm-agenda', [
            'contactos' => $this->getContactos(),
            // Ocupaciones ya cargadas, para sugerir en el formulario
            'ocupaciones' => ContactoAgenda::whereNotNull('ocupacion')->distinct()->orderBy('ocupacion')->pluck('ocupacion'),
        ]);
    }
}
