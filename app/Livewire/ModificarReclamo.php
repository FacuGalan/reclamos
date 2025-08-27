<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Edificio;
use Illuminate\Support\Collection;
use App\Models\Estado;
use App\Models\Persona;
use App\Models\Domicilios;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\MovimientoReclamoMail;

/**
 * @property Collection $categorias
 * @property Collection $categoriasFiltradas
 * @property Collection $edificios
 * @property Collection $edificiosFiltrados
 */
class ModificarReclamo extends Component
{
    public $editable = true;

    // ID del reclamo a modificar
    public $reclamoId;
    public $reclamo;
    public $userAreas = []; // Áreas del usuario logueado
    public $isPrivateArea = false; // Para indicar si el reclamo es de una área privada

    public $isSaving = false; // Para controlar el estado de guardado

    public $notificacionTimestamp = null;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success'; // 'success' o 'error'

    public $mostrarModal = false;
    public $noAplica = false; // Para indicar si el movimiento no aplica
    public $notificado = false; // Para indicar si se notificará al vecino
    public $nuevoMovimiento = false;
    public $derivar = false; // Para indicar si se está derivando el reclamo
    public $tiposMovimiento = '';
    public $areasDerivacion = [];
    public $nuevaArea = ''; // Área a la que se derivará el reclamo
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
    public $edificio_id = '';

    // Datos de la persona
    public $persona_dni = '';
    public $persona_nombre = '';
    public $persona_apellido = '';
    public $persona_telefono = '';
    public $persona_email = '';

    //Historial
    public $historial = [];
    public $historialTimestamp; // NUEVO: para forzar re-renderización

    // Control de flujo
    public $step = 1; // 1: datos persona, 2: datos reclamo, 3: confirmación
    public $showSuccess = false;
    
    // Datos para selects
    public $categorias = [];
    public $categoriasFiltradas = [];
    public $estados = [];
    public $areas = [];
    public $edificios = [];
    public $edificioBusqueda = '';
    public $edificioSeleccionado = null;
    public $edificiosFiltrados = [];

    // Nuevas propiedades para el searchable select
    public $categoriaBusqueda = '';
    public $mostrarDropdown = false;
    public $mostrarDropdownEdificios = false;
    public $categoriaSeleccionada = null;

    protected $rules = [
        'persona_dni' => 'required|numeric|digits_between:7,11',
        'persona_nombre' => 'required|string|max:255',
        'persona_apellido' => 'required|string|max:255',
        'persona_telefono' => 'required|numeric|digits_between:10,15',
        'persona_email' => 'nullable|email|max:255',
        'descripcion' => 'nullable|string|max:500',
        'direccion' => 'required|string|max:255',
        'entre_calles' => 'nullable|string|max:255',
        'coordenadas' => 'nullable|string',
        'categoria_id' => 'required|exists:categorias,id',
        'estado_id' => 'required|exists:estados,id',
        'edificio_id' => 'nullable|exists:edificios,id',
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
        'descripcion.max' => 'La descripción no puede exceder los 500 caracteres',
        'direccion.required' => 'La dirección es obligatoria',
        'categoria_id.required' => 'Debe seleccionar una categoría',
        'estado_id.required' => 'Debe seleccionar un estado',
    ];

    public function placeholder()
    {
      return view('livewire.placeholders.skeleton');
    }

    public function mount($reclamoId, $editable = true)
    {
        $this->reclamo = Reclamo::with(['categoria'])->find($this->reclamoId);
        
        if (!$this->reclamo) {
            session()->flash('error', 'Reclamo no encontrado.');
            return;
        }

        $this->isPrivateArea = $this->reclamo->categoria->privada;

        $this->editable = $editable;
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        $this->reclamoId = $reclamoId;
        $this->historialTimestamp = microtime(true); // Inicializar timestamp
        
        // Cargar datos para los selects
        $this->categorias = Categoria::where('privada', $this->isPrivateArea)
                                    ->whereIn('area_id', $this->userAreas)
                                    ->orderBy('nombre')->get();
        $this->categoriasFiltradas = $this->categorias;
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::orderBy('nombre')->get();
        $this->edificios = Edificio::orderBy('nombre')->get();

        // Cargar datos del reclamo
        $this->cargarDatosReclamo();

        // Cargar historial inicial
        $this->cargarHistorial();
    }

    public function cargarHistorial()
    {
        $this->historial = Movimiento::where('reclamo_id', $this->reclamoId)
            ->with(['tipoMovimiento', 'estado', 'usuario'])
            ->orderBy('id', 'desc')
            ->get();
        
        // IMPORTANTE: Actualizar timestamp para forzar re-renderización
        $this->historialTimestamp = microtime(true);
    }

    public function cargarDatosReclamo()
    {
        
        // Cargar datos del reclamo
        $this->descripcion = $this->reclamo->descripcion;
        $this->direccion = $this->reclamo->direccion;
        $this->entre_calles = $this->reclamo->entre_calles;
        $this->coordenadas = $this->reclamo->coordenadas;
        $this->categoria_id = $this->reclamo->categoria_id;
        $this->area_id = $this->reclamo->area_id;
        $this->area_nombre = $this->reclamo->area ? $this->reclamo->area->nombre : '';
        $this->estado_id = $this->reclamo->estado_id;
        $this->noAplica = $this->reclamo->no_aplica;
        $this->notificado = $this->reclamo->notificado; // Asignar valor de notificado si existe
        $this->edificio_id = $this->reclamo->edificio_id;
         
        // Configurar categoria seleccionada para el dropdown
        if ($this->categoria_id) {
            $categoria = $this->categorias->find($this->categoria_id);
            if ($categoria) {
                $this->categoriaBusqueda = $categoria->nombre;
                $this->categoriaSeleccionada = $categoria;
            }
        }
        if ($this->edificio_id) {
            $edificio = $this->edificios->find($this->edificio_id);
            if ($edificio) {
                $this->edificioBusqueda = $edificio->nombre;
                $this->edificioSeleccionado = $edificio;
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

    // Método para filtrar edificios cuando se escribe en el input
    public function updatedEdificioBusqueda()
    {
        if (empty($this->edificioBusqueda)) {
            $this->edificiosFiltrados = $this->edificios;
            $this->mostrarDropdownEdificios = false;
            $this->edificio_id = '';
            $this->edificioSeleccionado = null;
        } else {
            $this->edificiosFiltrados = $this->edificios->filter(function ($edificio) {
                return stripos($edificio->nombre, $this->edificioBusqueda) !== false;
            });
            $this->mostrarDropdownEdificios = true;
        }
    }

    // Método para seleccionar un edificio del dropdown
    public function seleccionarEdificio($edificioId)
    {
        $edificio = $this->edificios->find($edificioId);
        if ($edificio) {
            $this->edificio_id = $edificio->id;
            $this->edificioBusqueda = $edificio->nombre;
            $this->edificioSeleccionado = $edificio;
            $this->mostrarDropdownEdificios = false;
            $this->direccion = $edificio->direccion;

            // Limpiar errores de validación
            $this->resetErrorBag('edificio_id');
        }
    }

    public function mostrarTodosEdificios()
    {
        $this->edificiosFiltrados = $this->edificios;
        $this->mostrarDropdownEdificios = true;
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
        $this->tiposMovimiento = TipoMovimiento::
            where(function($query) {
                $query->where('area_id', $this->area_id)->orWhere('area_id', null);
            })
            ->where(function($query) {
                $query->where('id', 2)->orWhere('id', '>', 3);
            })
            ->orderBy('nombre')
            ->get();
        $this->mostrarModal = true;
        $this->nuevoMovimiento = true;
    }  

    public function derivarReclamo()
    {

        $this->areasDerivacion = Area::get();
        $this->derivar = true;
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->nuevoMovimiento = false;
        $this->tiposMovimiento = '';
        $this->derivar = false;
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

        // Validar observaciones si el estado no es finalizado (4)
        if($this->estadoMovimientoId != 4){
            $this->validate([
                'observaciones' => 'required|string|max:1000'
            ], [
                'observaciones.required' => 'Las observaciones son obligatorias a menos que sea un movimiento de Finalización.'
            ]);
        }
        

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
                'estado_id' => $this->estadoMovimientoId,
                'responsable_id' => Auth::id(), // Asignar el usuario actual como responsable
                'no_aplica' => $this->noAplica, // Actualizar el campo no_aplica
                'notificado' => $this->notificado // Actualizar el campo notificado
            ]);

            DB::commit();

            // En tu función guardarMovimiento, cambia esta parte:
            if ($this->notificado && !empty($this->reclamo->persona->email)) {
                try {
  
                    Mail::to($this->reclamo->persona->email)->send(new MovimientoReclamoMail($this->reclamo, $this->observaciones));
                
                    
                } catch (\Exception $emailException) {
                    Log::error('Error detallado al enviar email: ' . $emailException->getMessage(), [
                        'reclamo_id' => $this->reclamoId,
                        'email' => $this->reclamo->persona->email,
                        'trace' => $emailException->getTraceAsString()
                    ]);
                }
            }


            $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');

            // IMPORTANTE: Recargar después del commit
            $this->cargarHistorial();
            $this->cargarDatosReclamo();

            $this->cerrarModal();

            $this->dispatch('mensaje-toast', [
                'icon' => 'success',
                'text' => 'Nuevo movimiento creado',
            ]);

            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el movimiento: ' . $e->getMessage());
        }
    }

    public function guardarDerivacion()
    {
        // Validar campos de derivación
        $this->validate([
            'nuevaArea' => 'required|exists:areas,id',
        ]);

        $areaDerivada = Area::where('id',$this->nuevaArea)->get()->first();

        try {
            DB::beginTransaction();

            // Crear el movimiento de derivación
            Movimiento::create([
                'reclamo_id' => $this->reclamoId,
                'tipo_movimiento_id' => 3, // ID del tipo de movimiento "Derivación"
                'observaciones' => 'De '.$this->area_nombre.' a '.$areaDerivada->nombre.': ' . $this->observaciones,
                'fecha' => date('Y-m-d'),
                'usuario_id' => Auth::id(),
                'estado_id' => 3
            ]);

            // Actualizar el reclamo
            $this->reclamo->update([
                'area_id' => $this->nuevaArea,
                'estado_id' => 3, // Cambiar estado a "Derivado"
                'responsable_id' => NULL, // resetear el responsable al derivar
            ]);

            DB::commit();

            $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');
            
            //session()->flash('success', 'Reclamo derivado exitosamente');
            $this->cerrarModal();

            // Volver al ABM con un mensaje de éxito que se mostrará allí
            //session()->flash('reclamo_derivado', 'Reclamo derivado a '.$areaDerivada->nombre.' exitosamente');

            //$this->redirect(route('reclamos'), navigate: true);

             // Emitir evento que manejará el toast y la redirección
            $this->dispatch('reclamo-guardado-con-redirect', [
                'mensaje' => 'Reclamo derivado a '.$areaDerivada->nombre,
                'redirect_url' => route('reclamos')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al derivar el reclamo: ' . $e->getMessage());
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

            $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');

            // Activar notificación
            //$this->mostrarNotificacionExito();

            $this->isSaving = false;

            // Emitir evento local para mostrar el botón de éxito
            $this->dispatch('reclamo-modificado-exitoso');
            $this->dispatch('mensaje-toast', [
                'icon' => 'success',
                'text' => 'Reclamo actualizado',
            ]);
            
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

    // Agregar este método a tu clase ModificarReclamo
    public function prepararDatosImpresion()
    {
        \Log::info('Método prepararDatosImpresion iniciado');
        
        // ... mismo código que tenías en imprimirOrden, pero sin el dispatch
        // Solo la parte de guardar en sesión, sin el evento
        
        $reclamo = Reclamo::with([
            'persona', 'categoria', 'area', 'estado', 'edificio',
            'movimientos' => function($query) {
                $query->with(['tipoMovimiento', 'usuario'])->orderBy('created_at', 'desc');
            }
        ])->find($this->reclamoId);

        if (!$reclamo) {
            return false;
        }

        session(['datos_orden_impresion' => [
            'numero_orden' => str_pad($reclamo->id, 5, '0', STR_PAD_LEFT),
            'fecha_impresion' => now()->format('Y-m-d H:i:s'),
            'sector' => $reclamo->area->nombre ?? 'N/A',
            'motivo' => $reclamo->categoria->nombre ?? 'N/A',
            'numero_reclamo' => $reclamo->id,
            'fecha_reclamo' => $reclamo->created_at->format('Y-m-d - H:i:s'),
            'persona_nombre' => $reclamo->persona->nombre . ' ' . $reclamo->persona->apellido,
            'persona_telefono' => $reclamo->persona->telefono ?? 'N/A',
            'direccion' => $reclamo->direccion ?? 'N/A',
            'entre_calles' => $reclamo->entre_calles,
            'descripcion' => $reclamo->descripcion,
            'estado_actual' => $reclamo->estado->nombre ?? 'N/A',
            'historial' => $reclamo->movimientos->map(function($mov) {
                return [
                    'fecha' => $mov->created_at->format('Y-m-d (H:i)'),
                    'descripcion' => $mov->observaciones ?? 'Sin observaciones',
                    'usuario' => $mov->usuario->name ?? 'Sistema'
                ];
            })->toArray()
        ]]);
        
        \Log::info('Datos preparados para impresión');
        return true;
    }

    public function render()
    {
        return view('livewire.modificar-reclamo');
    }
}