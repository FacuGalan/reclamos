<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pregunta;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class AbmPreguntasFrecuentes extends Component
{
    use WithPagination;

    // Filtros
    public $busqueda = '';
    public $filtro_area = '';
    public $listaTimestamp = null;

    // Modal
    public $selectedPreguntaId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedPregunta = null;

    // Datos del usuario
    public $areas = [];
    public $userAreas = [];
    public $ver_privada = false;

    // Formulario
    public $pregunta = '';
    public $respuesta = '';
    public $area_id = '';
    public $privada = false;

    // Guardado y notificaciones
    public $isSaving = false;
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
            'pregunta' => $this->isEditing
                ? 'required|string|max:255|unique:preguntas,pregunta,' . $this->selectedPreguntaId
                : 'required|string|max:255|unique:preguntas,pregunta',
            'respuesta' => 'required|string',
            'area_id' => 'required|exists:areas,id',
        ];
    }

    protected $messages = [
        'pregunta.required' => 'El campo Pregunta es obligatorio',
        'pregunta.unique' => 'Ya existe una pregunta con este texto',
        'respuesta.required' => 'El campo Respuesta es obligatorio',
        'area_id.required' => 'Debe seleccionar un área',
        'area_id.exists' => 'El área seleccionada no es válida',
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['pregunta', 'respuesta', 'area_id'])) {
            $this->validateOnly($propertyName);
        }
    }

    public function mount()
    {
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();
        $this->ver_privada = Auth::user()->ver_privada;

        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->listaTimestamp = microtime(true);
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroArea()
    {
        $this->resetPage();
    }

    public function getPreguntas()
    {
        $query = Pregunta::with('area')
            ->whereIn('area_id', $this->userAreas)
            ->orderBy('id');

        if ($this->busqueda) {
            $query->where('pregunta', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('respuesta', 'like', '%' . $this->busqueda . '%');
        }

        if ($this->filtro_area && in_array($this->filtro_area, $this->userAreas)) {
            $query->where('area_id', $this->filtro_area);
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

    // Modales
    public function nuevaPregunta()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedPreguntaId = null;
        $this->showFormModal = true;

        if (count($this->userAreas) === 1) {
            $this->area_id = $this->userAreas[0];
        } else {
            $this->area_id = '';
        }
    }

    public function editarPregunta($id)
    {
        $pregunta = Pregunta::whereIn('area_id', $this->userAreas)->find($id);
        if (!$pregunta) {
            $this->mostrarNotificacionError('La pregunta no existe o no tienes permisos.');
            return;
        }

        $this->selectedPreguntaId = $id;
        $this->isEditing = true;
        $this->cargarDatosPregunta($pregunta);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedPreguntaId = null;
        $this->isEditing = false;
    }

    public function cargarDatosPregunta($pregunta)
    {
        $this->pregunta = $pregunta->pregunta;
        $this->respuesta = $pregunta->respuesta;
        $this->area_id = $pregunta->area_id;
    }

    public function resetForm()
    {
        $this->pregunta = '';
        $this->respuesta = '';
        $this->area_id = '';
        $this->privada = false;
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($id)
    {
        $pregunta = Pregunta::whereIn('area_id', $this->userAreas)->find($id);
        if (!$pregunta) {
            $this->mostrarNotificacionError('No tienes permisos para eliminar esta pregunta.');
            return;
        }

        $this->selectedPregunta = $pregunta;
        $this->showDeleteModal = true;
    }

    public function eliminarPregunta()
    {
        if ($this->selectedPregunta) {
            try {
                if (in_array($this->selectedPregunta->area_id, $this->userAreas)) {
                    $this->selectedPregunta->delete();
                    $this->showDeleteModal = false;
                    $this->selectedPregunta = null;
                    $this->mostrarNotificacionExito('Pregunta eliminada exitosamente');
                } else {
                    $this->mostrarNotificacionError('No tienes permisos para eliminar esta pregunta.');
                }
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar la pregunta: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedPregunta = null;
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
                Pregunta::create([
                    'pregunta' => $this->pregunta,
                    'respuesta' => $this->respuesta,
                    'area_id' => $this->area_id,
                ]);
                $mensaje = 'Pregunta creada exitosamente';
            } else {
                $pregunta = Pregunta::find($this->selectedPreguntaId);
                if (!in_array($pregunta->area_id, $this->userAreas)) {
                    $this->mostrarNotificacionError('No tienes permisos para editar esta pregunta.');
                    $this->isSaving = false;
                    return;
                }
                $pregunta->update([
                    'pregunta' => $this->pregunta,
                    'respuesta' => $this->respuesta,
                    'area_id' => $this->area_id,
                ]);
                $mensaje = 'Pregunta actualizada exitosamente';
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
        return view('livewire.abm-preguntas-frecuentes', [
            'preguntas' => $this->getPreguntas()
        ]);
    }
}