<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Secretaria;
use Illuminate\Validation\ValidationException;

class AbmSecretarias extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    
    // Propiedades para modales
    public $selectedSecretariaId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedSecretaria = null;

    // Datos del formulario
    public $nombre = '';

    // Estado de guardado
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
    ];

    // Reglas dinámicas basadas en el estado
    protected function rules()
    {
        return [
            'nombre' => $this->isEditing 
                ? 'required|string|max:255|unique:secretarias,nombre,' . $this->selectedSecretariaId
                : 'required|string|max:255|unique:secretarias,nombre',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la secretaría es obligatorio',
        'nombre.unique' => 'Ya existe una secretaría con este nombre',
        'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
    ];

    // NUEVO: Validación en tiempo real MÁS AGRESIVA
    public function updated($propertyName)
    {
        // Validar inmediatamente cuando cambie el nombre
        if ($propertyName === 'nombre') {
            $this->validateOnly($propertyName);
        }
    }

    // NUEVO: Validación cuando el campo pierde el foco
    public function updatedNombre()
    {
        $this->validateOnly('nombre');
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function getSecretarias()
    {
        $query = Secretaria::withCount('areas')
            ->orderBy('nombre');

        // Aplicar filtros
        if ($this->busqueda) {
            $query->where('nombre', 'like', '%' . $this->busqueda . '%');
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->resetPage();
    }

    // Métodos para modales
    public function nuevaSecretaria()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedSecretariaId = null;
        $this->showFormModal = true;
    }

    public function editarSecretaria($secretariaId)
    {
        $secretaria = Secretaria::find($secretariaId);
        if (!$secretaria) {
            $this->mostrarNotificacionError('La secretaría solicitada no existe.');
            return;
        }

        $this->selectedSecretariaId = $secretariaId;
        $this->isEditing = true;
        $this->cargarDatosSecretaria($secretaria);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedSecretariaId = null;
        $this->isEditing = false;
    }

    public function cargarDatosSecretaria($secretaria)
    {
        $this->nombre = $secretaria->nombre;
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->isSaving = false;
        $this->resetErrorBag(); // Solo se resetea cuando se cierra el modal
    }

    public function confirmarEliminacion($secretariaId)
    {
        $this->selectedSecretaria = Secretaria::withCount('areas')->find($secretariaId);
        $this->showDeleteModal = true;
    }

    public function eliminarSecretaria()
    {
        if ($this->selectedSecretaria) {
            try {
                // Verificar si tiene áreas asociadas
                if ($this->selectedSecretaria->areas_count > 0) {
                    $this->mostrarNotificacionError('No se puede eliminar la secretaría porque tiene áreas asociadas.');
                    $this->showDeleteModal = false;
                    $this->selectedSecretaria = null;
                    return;
                }

                $this->selectedSecretaria->delete();
                $this->showDeleteModal = false;
                $this->selectedSecretaria = null;
                
                $this->mostrarNotificacionExito('Secretaría eliminada exitosamente');
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar la secretaría: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedSecretaria = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            // Validar usando las reglas dinámicas
            $this->validate();

            if (!$this->isEditing) {
                Secretaria::create([
                    'nombre' => $this->nombre,
                ]);
                
                $mensaje = 'Secretaría creada exitosamente';
            } else {
                $secretaria = Secretaria::find($this->selectedSecretariaId);
                $secretaria->update([
                    'nombre' => $this->nombre,
                ]);
                
                $mensaje = 'Secretaría actualizada exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // CLAVE: NO hagas return aquí, vuelve a lanzar la excepción
            $this->isSaving = false;
            throw $e; // ← ESTO es lo que faltaba
            
        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar la secretaría: ' . $e->getMessage());
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
        $secretarias = $this->getSecretarias();
        
        return view('livewire.abm-secretarias', [
            'secretarias' => $secretarias
        ]);
    }
}