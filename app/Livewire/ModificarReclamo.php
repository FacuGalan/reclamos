<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Estado;
use App\Models\Persona;
use App\Models\Domicilios;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @property Collection $categorias
 * @property Collection $categoriasFiltradas
 */
class ModificarReclamo extends Component
{
    // ID del reclamo a modificar
    public $reclamoId;
    public $reclamo;
    public $userAreas = []; // Áreas del usuario logueado

    public $isSaving = false; // Para controlar el estado de guardado

    public $notificacionTimestamp = null;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success'; // 'success' o 'error'

    public $mostrarModal = false;
    public $nuevoMovimiento = false;
    public $tiposMovimiento = '';
    public $tipoMovimientoId = null; // ID del tipo de movimiento seleccionado
    public $observaciones = '';
    public $fechaMovimiento = '';
    public $usuarioId = null; // ID del usuario que realiza el movimiento, se asigna automáticamente
    public $estadoMovimiento = null; // Estado del reclamo al momento del movimiento
    public $estadoMovimientoId = null; // ID del estado del reclamo al momento del movimiento

    // Datos del reclamo
    public $descripcion = '';
    public $direccion = '';
    public $entre_calles = '';
    public $coordenadas = '';
    public $area_id = '';
    public $area_nombre = '';
    public $categoria_id = '';
    public $estado_id = '';
    
    // Datos de la persona
    public $persona_dni = '';
    public $persona_nombre = '';
    public $persona_apellido = '';
    public $persona_telefono = '';
    public $persona_email = '';

    //Historial
    public $historial = [];
    
    // Control de flujo
    public $step = 1; // 1: datos persona, 2: datos reclamo, 3: confirmación
    public $showSuccess = false;
    
    // Datos para selects
    public $categorias = [];
    public $categoriasFiltradas = [];
    public $estados = [];
    public $areas = [];

    // Nuevas propiedades para el searchable select
    public $categoriaBusqueda = '';
    public $mostrarDropdown = false;
    public $categoriaSeleccionada = null;

    protected $rules = [
        'persona_dni' => 'required|numeric|digits_between:7,11',
        'persona_nombre' => 'required|string|max:255',
        'persona_apellido' => 'required|string|max:255',
        'persona_telefono' => 'required|numeric|digits_between:10,15',
        'persona_email' => 'nullable|email|max:255',
        'descripcion' => 'required|string|max:1000',
        'direccion' => 'required|string|max:255',
        'entre_calles' => 'nullable|string|max:255',
        'coordenadas' => 'nullable|string',
        'categoria_id' => 'required|exists:categorias,id',
        'estado_id' => 'required|exists:estados,id',
    ];

    protected $messages = [
        'persona_dni.required' => 'El DNI es obligatorio',
        'persona_dni.numeric' => 'El DNI debe contener solo números',
        'persona_dni.digits_between' => 'El DNI debe tener entre 7 y 11 dígitos',
        'persona_nombre.required' => 'El nombre es obligatorio',
        'persona_apellido.required' => 'El apellido es obligatorio',
        'persona_telefono.digits_between' => 'El teléfono debe tener entre 10 y 15 dígitos',
        'persona_email.email' => 'Ingrese un email válido',
        'descripcion.required' => 'La descripción del reclamo es obligatoria',
        'descripcion.max' => 'La descripción no puede exceder los 1000 caracteres',
        'direccion.required' => 'La dirección es obligatoria',
        'categoria_id.required' => 'Debe seleccionar una categoría',
        'estado_id.required' => 'Debe seleccionar un estado',
    ];

    public function placeholder()
    {
      return view('livewire.placeholders.skeleton');
    }

    public function mount($reclamoId)
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        $this->reclamoId = $reclamoId;
        
        // Cargar datos para los selects
        $this->categorias = Categoria::whereIn('area_id', $this->userAreas)
                                    ->orderBy('nombre')->get();
        $this->categoriasFiltradas = $this->categorias;
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::orderBy('nombre')->get();
        $this->historial = Movimiento::where('reclamo_id', $this->reclamoId)
            ->with(['tipoMovimiento', 'estado', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        
        // Cargar datos del reclamo
        $this->cargarDatosReclamo();
    }

    public function cargarDatosReclamo()
    {
        $this->reclamo = Reclamo::with(['persona', 'domicilio', 'categoria', 'estado', 'area'])
            ->find($this->reclamoId);
        
        if (!$this->reclamo) {
            session()->flash('error', 'Reclamo no encontrado.');
            return;
        }

        // Cargar datos del reclamo
        $this->descripcion = $this->reclamo->descripcion;
        $this->direccion = $this->reclamo->direccion;
        $this->entre_calles = $this->reclamo->entre_calles;
        $this->coordenadas = $this->reclamo->coordenadas;
        $this->categoria_id = $this->reclamo->categoria_id;
        $this->area_id = $this->reclamo->area_id;
        $this->area_nombre = $this->reclamo->area ? $this->reclamo->area->nombre : '';
        $this->estado_id = $this->reclamo->estado_id;

        // Configurar categoria seleccionada para el dropdown
        if ($this->categoria_id) {
            $categoria = $this->categorias->find($this->categoria_id);
            if ($categoria) {
                $this->categoriaBusqueda = $categoria->nombre;
                $this->categoriaSeleccionada = $categoria;
            }
        }

        // Cargar datos de la persona
        if ($this->reclamo->persona) {
            $persona = $this->reclamo->persona;
            $this->persona_dni = $persona->dni;
            $this->persona_nombre = $persona->nombre;
            $this->persona_apellido = $persona->apellido;
            $this->persona_telefono = $persona->telefono;
            $this->persona_email = $persona->email;
        }
    }

    // Método para filtrar categorías cuando se escribe en el input
    public function updatedCategoriaBusqueda()
    {
        if (empty($this->categoriaBusqueda)) {
            $this->categoriasFiltradas = $this->categorias;
            $this->mostrarDropdown = false;
            $this->categoria_id = '';
            $this->categoriaSeleccionada = null;
        } else {
            $this->categoriasFiltradas = $this->categorias->filter(function ($categoria) {
                return stripos($categoria->nombre, $this->categoriaBusqueda) !== false;
            });
            $this->mostrarDropdown = true;
        }
    }

    // Método para seleccionar una categoría del dropdown
    public function seleccionarCategoria($categoriaId)
    {
        $categoria = $this->categorias->find($categoriaId);
        if ($categoria) {
            $this->categoria_id = $categoria->id;
            $this->categoriaBusqueda = $categoria->nombre;
            $this->categoriaSeleccionada = $categoria;
            $this->mostrarDropdown = false;
            
            // Actualizar área automáticamente
            $this->area_id = $categoria->area_id;
            $this->area_nombre = $categoria->area ? $categoria->area->nombre : '';
            
            // Limpiar errores de validación
            $this->resetErrorBag('categoria_id');
        }
    }

    // Método para mostrar todas las categorías cuando se hace clic en el input
    public function mostrarTodasCategorias()
    {
        $this->categoriasFiltradas = $this->categorias;
        $this->mostrarDropdown = true;
    }

    public function nextStep()
    {
        if ($this->step == 1) {
            $this->validateStep1();
            $this->step = 2;
        } elseif ($this->step == 2) {
            $this->validateStep2();
            $this->step = 3;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateStep1()
    {
        $this->validate([
            'persona_dni' => $this->rules['persona_dni'],
            'persona_nombre' => $this->rules['persona_nombre'],
            'persona_apellido' => $this->rules['persona_apellido'],
            'persona_telefono' => $this->rules['persona_telefono'],
            'persona_email' => $this->rules['persona_email'],
        ]);
    }

    public function validateStep2()
    {
        $this->validate([
            'descripcion' => $this->rules['descripcion'],
            'direccion' => $this->rules['direccion'],
            'entre_calles' => $this->rules['entre_calles'],
            'coordenadas' => $this->rules['coordenadas'],
            'categoria_id' => $this->rules['categoria_id'],
            'estado_id' => $this->rules['estado_id'],
        ]);
    }

    public function nuevoMovimiento1()
    {
        $this->tiposMovimiento = TipoMovimiento::where('area_id', $this->area_id)
            ->orderBy('nombre')
            ->get();
        $this->mostrarModal = true;
    }  

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->nuevoMovimiento = false;
        $this->tiposMovimiento = '';
        $this->observaciones = '';
        $this->fechaMovimiento = '';
        $this->usuarioId = null;
        $this->estadoMovimiento = null;
        $this->estadoMovimientoId = null;
    }

    public function guardarMovimiento()
    {
        // Validar campos del movimiento
        $this->validate([
            'tipoMovimientoId' => 'required|exists:tipo_movimientos,id',
            'observaciones' => 'nullable|string|max:1000'
        ]);


        $this->fechaMovimiento = date('Y-m-d'); // Asignar fecha actual si no se especifica
        $this->usuarioId = Auth::id(); // Asignar el ID del usuario
        $this->estadoMovimientoId = TipoMovimiento::where('id',$this->tipoMovimientoId)->first()->estado_id; // Obtener el estado del movimiento

        try {
            DB::beginTransaction();

            // Crear el movimiento
            Movimiento::create([
                'reclamo_id' => $this->reclamoId,
                'tipo_movimiento_id' => $this->tipoMovimientoId,
                'observaciones' => $this->observaciones,
                'fecha' => $this->fechaMovimiento,
                'usuario_id' => $this->usuarioId,
                'estado_id' => $this->estadoMovimientoId
            ]);
         

            // Actualizar el reclamo
            $this->reclamo->update([
                'estado_id' => $this->estadoMovimientoId
            ]);



            DB::commit();
            session()->flash('success', 'Movimiento guardado exitosamente');
            $this->cerrarModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el movimiento: ' . $e->getMessage());
        }
    }

    public function save()
    {
        $this->isSaving = true; // Indicar que se está guardando

        // Validar todos los campos
        $this->validate();

        try {
            DB::beginTransaction();

            // Actualizar datos de la persona
            $persona = $this->reclamo->persona;
            $persona->update([
                'dni' => $this->persona_dni,
                'nombre' => $this->persona_nombre,
                'apellido' => $this->persona_apellido,
                'telefono' => $this->persona_telefono,
                'email' => $this->persona_email,
            ]);

            // Actualizar domicilio
            $domicilio = $this->reclamo->domicilio;
            if ($domicilio) {
                $domicilio->update([
                    'direccion' => $this->direccion,
                    'entre_calles' => $this->entre_calles,
                    'coordenadas' => $this->coordenadas,
                ]);
            }

            // Actualizar el reclamo
            $this->reclamo->update([
                'descripcion' => $this->descripcion,
                'direccion' => $this->direccion,
                'entre_calles' => $this->entre_calles,
                'coordenadas' => $this->coordenadas,
                'area_id' => $this->area_id,
                'categoria_id' => $this->categoria_id,
                'estado_id' => $this->estado_id,
            ]);

            DB::commit();
            /*
            // Emitir evento para notificar el éxito
            $this->dispatch('reclamo-actualizado', [
                'id' => $this->reclamo->id,
                'message' => 'Reclamo actualizado exitosamente'
            ]);

            // Emitir evento para cerrar modal si se usa desde el ABM
            $this->dispatch('reclamo-saved');

            $this->showSuccess = true;
            */

            // Activar notificación
            $this->mostrarNotificacionExito();
                
            // Redirección inmediata sin delay
            //$this->redirect(route('reclamos'), navigate: true);

            $this->isSaving = false;

            // Emitir evento local para mostrar el botón de éxito
            $this->dispatch('reclamo-modificado-exitoso');
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Mostrar notificación de error
            $this->mostrarNotificacionError('Error al actualizar el reclamo: ' . $e->getMessage());
        }
    }

    public function mostrarNotificacionExito($mensaje = 'Reclamo actualizado exitosamente')
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'success';
        $this->notificacionTimestamp = microtime(true); // Esto fuerza que el blade se re-renderice
    }

    public function mostrarNotificacionError($mensaje)
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'error';
        $this->notificacionTimestamp = microtime(true); // Esto fuerza que el blade se re-renderice
    }

    // También agrega esta función para limpiar la notificación cuando sea necesario
    public function limpiarNotificacion()
    {
        $this->mostrarNotificacion = false;
        $this->mensajeNotificacion = '';
    }

    public function render()
    {
        $this->historial = Movimiento::where('reclamo_id', $this->reclamoId)
            ->with(['tipoMovimiento', 'estado', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();
            
        return view('livewire.modificar-reclamo');
    }
}