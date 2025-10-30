<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ModeloExportacionReclamo;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class AdminModelosExportacion extends Component
{
    public $mostrarModal = false;
    public $modoEdicion = false;
    public $modeloEditandoId = null;

    public $nombre = '';
    public $area_id = '';
    public $camposSeleccionados = ['id']; // ID siempre está seleccionado
    public $areasDisponibles = [];

    public $camposDisponibles = [
        'id' => 'ID',
        'fecha' => 'Fecha',
        'nombre_persona' => 'Nombre',
        'apellido_persona' => 'Apellido',
        'dni' => 'DNI',
        'telefono' => 'Teléfono',
        'email' => 'Email',
        'categoria' => 'Categoría',
        'area' => 'Área',
        'numero_tranquera' => 'Tranquera',
        'direccion' => 'Dirección',
        'entre_calles' => 'Entre calles',
        'direccion_rural' => 'Aclaración Dirección',
        'barrio' => 'Barrio',
        'estado' => 'Estado',
        'usuario_creador' => 'Usuario Creador',
        'responsable' => 'Responsable',
        'fecha_creacion' => 'Fecha Creación',
        'descripcion' => 'Descripción',
    ];

    public function mount()
    {
        // Verificar que el usuario sea administrador de su área y tenga rol <= 3
        if (!Auth::user()->rol->lReclamosAlta || Auth::user()->rol_id > 3) {
            abort(403, 'No tienes permisos para administrar modelos de exportación.');
        }

        // Cargar las áreas del usuario
        $this->areasDisponibles = Auth::user()->areas;
    }

    public function render()
    {
        $modelos = ModeloExportacionReclamo::with(['area', 'usuarioCreador'])
            ->whereIn('area_id', Auth::user()->areas->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin-modelos-exportacion', [
            'modelos' => $modelos
        ]);
    }

    public function abrirModal()
    {
        $this->reset(['nombre', 'area_id', 'camposSeleccionados', 'modoEdicion', 'modeloEditandoId']);
        $this->camposSeleccionados = ['id']; // ID siempre seleccionado
        $this->mostrarModal = true;
    }

    public function editarModelo($id)
    {
        $modelo = ModeloExportacionReclamo::findOrFail($id);

        // Verificar permisos
        if (!Auth::user()->areas->pluck('id')->contains($modelo->area_id)) {
            session()->flash('error', 'No tienes permisos para editar este modelo.');
            return;
        }

        $this->modoEdicion = true;
        $this->modeloEditandoId = $modelo->id;
        $this->nombre = $modelo->nombre;
        $this->area_id = $modelo->area_id;
        $this->camposSeleccionados = $modelo->campos;
        $this->mostrarModal = true;
    }

    public function guardarModelo()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'camposSeleccionados' => 'required|array|min:1',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'area_id.required' => 'Debes seleccionar un área',
            'area_id.exists' => 'El área seleccionada no es válida',
            'camposSeleccionados.required' => 'Debes seleccionar al menos un campo',
            'camposSeleccionados.min' => 'Debes seleccionar al menos un campo',
        ]);

        // Verificar que el usuario tenga acceso al área seleccionada
        if (!Auth::user()->areas->pluck('id')->contains($this->area_id)) {
            session()->flash('error', 'No tienes acceso al área seleccionada.');
            return;
        }

        // Asegurar que ID siempre esté incluido
        if (!in_array('id', $this->camposSeleccionados)) {
            $this->camposSeleccionados[] = 'id';
        }

        if ($this->modoEdicion) {
            $modelo = ModeloExportacionReclamo::findOrFail($this->modeloEditandoId);

            // Verificar permisos
            if (!Auth::user()->areas->pluck('id')->contains($modelo->area_id)) {
                session()->flash('error', 'No tienes permisos para editar este modelo.');
                return;
            }

            $modelo->update([
                'nombre' => $this->nombre,
                'area_id' => $this->area_id,
                'campos' => $this->camposSeleccionados,
            ]);

            session()->flash('mensaje', 'Modelo actualizado exitosamente.');
        } else {
            ModeloExportacionReclamo::create([
                'nombre' => $this->nombre,
                'area_id' => $this->area_id,
                'campos' => $this->camposSeleccionados,
                'usuario_creador_id' => Auth::id(),
            ]);

            session()->flash('mensaje', 'Modelo creado exitosamente.');
        }

        $this->cerrarModal();
    }

    public function eliminarModelo($id)
    {
        $modelo = ModeloExportacionReclamo::findOrFail($id);

        // Verificar permisos
        if (!Auth::user()->areas->pluck('id')->contains($modelo->area_id)) {
            session()->flash('error', 'No tienes permisos para eliminar este modelo.');
            return;
        }

        $modelo->delete();
        session()->flash('mensaje', 'Modelo eliminado exitosamente.');
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->reset(['nombre', 'area_id', 'camposSeleccionados', 'modoEdicion', 'modeloEditandoId']);
        $this->camposSeleccionados = ['id'];
    }

    public function toggleCampo($campo)
    {
        if ($campo === 'id') {
            // ID no se puede deseleccionar
            return;
        }

        if (in_array($campo, $this->camposSeleccionados)) {
            $this->camposSeleccionados = array_values(array_diff($this->camposSeleccionados, [$campo]));
        } else {
            $this->camposSeleccionados[] = $campo;
        }
    }
}
