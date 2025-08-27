<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Edificio;
use App\Models\Estado;
use App\Models\Persona;
use App\Models\Domicilios;
use App\Models\Movimiento;
use App\Models\Barrio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Mail\ReclamoConfirmacion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * @property Collection $categorias
 * @property Collection $categoriasFiltradas
 * @property Collection $edificios
 * @property Collection $edificiosFiltrados
 */

class AltaReclamo extends Component
{
    public $contexto = 'publico';
    public $datosPrecargados = [];

    // Datos del reclamo
    public $descripcion = '';
    public $direccion = '';
    public $entre_calles = '';
    public $coordenadas = '';
    public $area_id = '';
    public $categoria_id = '';
    public $edificio_id = '';
    public $userAreas = []; // Áreas del usuario autenticado

    // Nueva propiedad para el historial de reclamos
    public $reclamosPersona = [];
    public $personaId = null; // ID de la persona encontrada
    public $mostrarModalDetalle = false;
    public $reclamoDetalle = null;
    public $movimientosDetalle = [];
    
    // Datos de la persona
    public $persona_dni = '';
    public $persona_nombre = '';
    public $persona_apellido = '';
    public $persona_telefono = '';
    public $persona_email = '';
    public $persona_domicilios = []; // Domicilios de la persona

    public $mostrar_inputs_direccion = true;
    public $domicilio_id = null; // ID del domicilio seleccionado
    
    // Control de flujo
    public $step = 1; // 1: datos persona, 2: datos reclamo, 3: confirmación
    public $showSuccess = false;
    public $reclamoCreado = null;
    
    // Datos para selects
    public $categorias = [];
    public $categoriasFiltradas = [];
    public $edificios = [];
    public $edificiosFiltrados = [];
    
    // Props para reutilización
    public $showPersonaForm = true; // Si false, usa el usuario autenticado
    public $redirectAfterSave = null; // URL de redirección después de guardar
    public $successMessage = 'Reclamo creado exitosamente';
    
    // Nueva propiedad para determinar el contexto
    public $isPrivateArea = false; // true cuando se llama desde el área privada

    public $personaEncontrada = false;

    // Nuevas propiedades para el searchable select
    public $categoriaBusqueda = '';
    public $mostrarDropdown = false;
    public $categoriaSeleccionada = null;
    public $edificioSeleccionado = null;
    public $edificioBusqueda = '';

    // Nueva propiedad para el estado de guardado
    public $isSaving = false;

    // Propiedades para el mapa
    public $mostrarMapa = false;
    public $latitud = null;
    public $longitud = null;
    public $direccionCompleta = '';

    public $barrio_encontrado;
    

    // Agregar esta propiedad a tu clase
    protected $listeners = [
        'confirmar-ubicacion-mapa' => 'confirmarUbicacionMapa'
    ];

    protected $rules = [
        'persona_dni' => 'required|numeric|digits:8',
        'persona_nombre' => 'required|string|max:255',
        'persona_apellido' => 'required|string|max:255',
        'persona_telefono' => 'required|numeric|digits_between:10,15',
        'persona_email' => 'nullable|email|max:255',
        'descripcion' => 'nullable|string|max:500',
        'direccion' => 'required|string|max:255',
        'entre_calles' => 'nullable|string|max:255',
        'coordenadas' => 'required|string',
        'categoria_id' => 'required|exists:categorias,id',
        'edificio_id' => 'required|exists:edificios,id',
    ];

    protected $messages = [
        'persona_dni.required' => 'El DNI es obligatorio',
        'persona_dni.numeric' => 'El DNI debe contener solo números',
        'persona_dni.digits' => 'El DNI debe tener 8 dígitos',
        'persona_nombre.required' => 'El nombre es obligatorio',
        'persona_apellido.required' => 'El apellido es obligatorio',
        'persona_telefono.required' => 'El teléfono es obligatorio',
        'persona_telefono.digits_between' => 'El teléfono debe tener 10 dígitos',
        'persona_email.email' => 'Ingrese un email válido',
        'direccion.required' => 'La dirección es obligatoria',
        'coordenadas.required' => 'Dirección no validada - Use el mapa para mayor precisión',
        'descripcion.max' => 'La descripción no puede exceder los 500 caracteres',
        'categoria_id.required' => 'Debe seleccionar una categoría',
        'edificio_id.required' => 'Debe seleccionar un edificio',
    ];

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        if (Auth::check()) {
                $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

            // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
            if (empty($this->userAreas)) {
                $this->userAreas = Area::pluck('id')->toArray();
            }
        } else {
            $this->userAreas = Area::pluck('id')->toArray(); // Todas las áreas si no está autenticado
        }
        $this->categorias = Categoria::where('privada', $this->isPrivateArea)
                                    ->whereIn('area_id', $this->userAreas)
                                    ->orderBy($this->contexto === 'publico' ? 'nombre_publico' : 'nombre')
                                    ->get();

        $this->categoriasFiltradas = $this->categorias;

        $this->edificios = Edificio::orderBy('nombre')->get();
        $this->edificiosFiltrados = $this->edificios;

        if (!empty($this->datosPrecargados)) {
            $this->persona_dni = $this->datosPrecargados['dni'] ?? '';
            $this->persona_nombre = $this->datosPrecargados['nombre'] ?? '';
            $this->persona_apellido = $this->datosPrecargados['apellido'] ?? '';
            
            // Si hay datos precargados, buscar si la persona ya existe en el sistema
            if ($this->persona_dni) {
                $this->buscarPersonaPorDni();
            }
            
            // Saltar al paso 2 directamente si no se muestra el form de persona
            if (!$this->showPersonaForm) {
                $this->step = 2;
            }
        }
        
        // Si el usuario está autenticado y no se quiere mostrar el form de persona
        if (!$this->showPersonaForm && Auth::check()) {
            $user = Auth::user();
            $this->persona_dni = $user->dni;
            $this->persona_nombre = explode(' ', $user->name)[0] ?? '';
            $this->persona_apellido = explode(' ', $user->name, 2)[1] ?? '';
            $this->persona_telefono = $user->telefono;
            $this->persona_email = $user->email;
            $this->step = 2; // Saltar al paso 2
        }
    }

    public function abrirMapa()
    {
        // Limpiar estado anterior parcialmente (mantener coordenadas si existen)
        if (empty($this->coordenadas)) {
            $this->latitud = null;
            $this->longitud = null;
            $this->direccionCompleta = '';
        }
        
        $this->mostrarMapa = true;
        
        // Debugging: log que se está abriendo el mapa
        logger('Abriendo mapa modal - Coordenadas actuales: ' . $this->coordenadas);
        
        // Disparar evento después de que el modal se renderice
        $this->dispatch('inicializar-mapa');
    }

    public function cerrarMapa()
    {
        $this->mostrarMapa = false;
        
        // Debugging: log que se está cerrando el mapa
        logger('Cerrando mapa modal');
    }

    // AGREGAR este método nuevo para debugging
    public function testMapa()
    {
        logger('Test del mapa ejecutado');
        $this->dispatch('test-mapa');
    }

    public function syncronizarPosicionMapa()
    {
        // Este método puede ser llamado desde JavaScript para obtener las coordenadas actuales
        if (!empty($this->coordenadas)) {
            //$this->direccion = $this->direccionCompleta;
            return [
                'coordenadas' => $this->coordenadas,
                'direccion' => $this->direccion,
                'direccionCompleta' => $this->direccionCompleta
            ];
        }
        return null;
    }

    #[Livewire\Attributes\On('confirmar-ubicacion-mapa')]
    public function confirmarUbicacionMapa($lat, $lng, $direccion)
    {
        $this->confirmarUbicacion($lat, $lng, $direccion);
    }

     // Método para confirmar la ubicación del mapa
    public function confirmarUbicacion($latitud, $longitud, $direccion)
    {
        $this->latitud = $latitud;
        $this->longitud = $longitud;
        $this->direccionCompleta = $direccion;
        $this->coordenadas = $latitud . ',' . $longitud;
        
        // Actualizar la dirección si es mejor que la actual
        //if (empty($this->direccion) || strlen($direccion) > strlen($this->direccion)) {
            $this->direccion = $direccion;
        //}
        
        $this->mostrarMapa = false;

        
        $this->dispatch('ubicacion-confirmada', [
            'direccion' => $direccion,
            'coordenadas' => $this->coordenadas
        ]);
    }

    // Método para ocultar el dropdown
    public function ocultarDropdown()
    {
        // Delay para permitir que se registre el click en una opción
        $this->dispatch('delay-hide-dropdown');
    }

    // DNI: Se ejecuta cuando cambia el DNI
    public function updatedPersonaDni()
    {
        // Limpiar errores previos del DNI
        $this->resetErrorBag('persona_dni');
        
        // Solo buscar si el DNI tiene al menos 7 dígitos y es numérico
        if (strlen($this->persona_dni) >= 7 && is_numeric($this->persona_dni)) {
            $this->buscarPersonaPorDni();
        } else {
            // Si el DNI no es válido, limpiar los campos
            $this->limpiarDatosPersona();
        }
    }

    // DNI: Buscar persona por DNI
    public function buscarPersonaPorDni()
    {
        try {
            $persona = Persona::where('dni', $this->persona_dni)->first();
            
            if ($persona) {
                // Persona encontrada - completar campos automáticamente
                $this->persona_nombre = $persona->nombre;
                $this->persona_apellido = $persona->apellido;
                $this->persona_telefono = $persona->telefono;
                $this->persona_email = $persona->email;
                $this->personaEncontrada = true;
                $this->personaId = $persona->id; // Guardar el ID de la persona
                
                // Cargar domicilios de la persona
                $this->persona_domicilios = Domicilios::where('persona_id', $persona->id)->get();
                $this->domicilio_id = null; // Limpiar ID de domicilio seleccionado
                
                if($this->persona_domicilios->isEmpty()) {
                    // Si no tiene domicilios, inicializar como un array vacío
                    $this->persona_domicilios = [];
                    $this->mostrar_inputs_direccion = true;
                }else {
                    // Si tiene domicilios, NO mostrar inputs de dirección
                    $this->mostrar_inputs_direccion = false;
                }
                
                // Cargar historial de reclamos de esta persona
                if ($this->contexto === 'privado' || $this->contexto === 'externo') {
                    $this->cargarReclamosPersona($persona->id);
                }
                
                
                // Emitir evento de JavaScript para mostrar notificación
                $this->dispatch('persona-encontrada', [
                    'mensaje' => 'Persona encontrada en el sistema. Datos completados automáticamente.'
                ]);
                
            } else {
                // Persona no encontrada - limpiar campos
                $this->limpiarDatosPersona();
                $this->personaEncontrada = false;
                $this->personaId = null;
                $this->reclamosPersona = []; // Limpiar historial
                $this->persona_domicilios = []; // Limpiar domicilios
                $this->mostrar_inputs_direccion = true; // Mostrar inputs de dirección
                $this->domicilio_id = null; // Limpiar ID de domicilio
            }
        } catch (\Exception $e) {
            // En caso de error, limpiar campos
            $this->limpiarDatosPersona();
            $this->personaEncontrada = false;
            $this->personaId = null;
            $this->reclamosPersona = [];
        }
    }

    public function updatedDomicilioId($value)
    {
        
        if ($value === 'nuevo' || $value === '' || is_null($value)) {
            $this->mostrar_inputs_direccion = true;
         
            // Limpiar campos para ingresar nuevos datos
            $this->direccion = '';
            $this->coordenadas = '';
            $this->entre_calles = '';
        } else {
            // Eligió un domicilio existente, ocultar inputs
            $this->mostrar_inputs_direccion = false;

            // Buscar el domicilio y autocompletar
            $domicilio = Domicilios::find($value);
            if ($domicilio) {
                $this->direccion = $domicilio->direccion;
                $this->coordenadas = $domicilio->coordenadas;
                $this->entre_calles = $domicilio->entre_calles;
            }
        }
    }

    public function cargarReclamosPersona($personaId)
    {
        $this->reclamosPersona = Reclamo::where('persona_id', $personaId)
            ->whereHas('categoria', function ($q) {
                $q->where('privada', $this->isPrivateArea);
            })
            ->with(['categoria', 'area', 'estado']) // Cargar relaciones
            ->orderBy('created_at', 'desc') // Más recientes primero
            ->limit(10) // Limitar a los últimos 10 reclamos
            ->get();
    }

    // DNI: Limpiar datos de persona
    private function limpiarDatosPersona()
    {
        $this->persona_nombre = '';
        $this->persona_apellido = '';
        $this->persona_telefono = '';
        $this->persona_email = '';
        $this->personaEncontrada = false;
        $this->personaId = null;
        $this->reclamosPersona = []; // Limpiar historial
    }


    public function verDetalleReclamo($reclamoId)
    {
        // Cargar reclamo con todas sus relaciones
        $this->reclamoDetalle = Reclamo::with([
            'categoria', 
            'area', 
            'estado', 
            'persona',
            'usuario', // Usuario que creó el reclamo
            'responsable' // Usuario responsable actual
        ])->find($reclamoId);
        
        if ($this->reclamoDetalle) {
            // Cargar movimientos del reclamo con sus relaciones
            $this->movimientosDetalle = \App\Models\Movimiento::where('reclamo_id', $reclamoId)
                ->with([
                    'tipoMovimiento',
                    'estado', 
                    'usuario'
                ])
                ->orderBy('fecha', 'desc') // Más recientes primero
                ->orderBy('created_at', 'desc')
                ->get();
                
            $this->mostrarModalDetalle = true;
        }
    }


    // NUEVA FUNCIÓN: Cerrar modal de detalle
    public function cerrarModalDetalle()
    {
        $this->mostrarModalDetalle = false;
        $this->reclamoDetalle = null;
        $this->movimientosDetalle = []; // Limpiar movimientos
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
        if ($this->showPersonaForm) {
            $this->validate([
                'persona_dni' => $this->rules['persona_dni'],
                'persona_nombre' => $this->rules['persona_nombre'],
                'persona_apellido' => $this->rules['persona_apellido'],
                'persona_telefono' => $this->rules['persona_telefono'],
                'persona_email' => $this->rules['persona_email'],
            ]);
        }
    }

    public function validateStep2()
    {
        if($this->isPrivateArea){
            $this->validate([
                'descripcion' => $this->rules['descripcion'],
                'categoria_id' => $this->rules['categoria_id'],
                'edificio_id' => $this->rules['edificio_id'],
            ]);
        }else{
            $this->validate([
                'descripcion' => $this->rules['descripcion'],
                'direccion' => $this->rules['direccion'],
                'entre_calles' => $this->rules['entre_calles'],
                'coordenadas' => $this->rules['coordenadas'],
                'categoria_id' => $this->rules['categoria_id'],
            ]);

        }
        
    }

    public function obtenerBarrioPorCoordenadas($coordenadas)
    {
        if (empty($coordenadas)) {
            return null;
        }
        
        $coords = explode(',', $coordenadas);
        if (count($coords) != 2) {
            return null;
        }
        
        $lat = trim($coords[0]);
        $lng = trim($coords[1]);
        
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }
        
        // Construir el punto directamente en la consulta
        $punto = "POINT({$lng} {$lat})";
        
        $barrio = DB::selectOne("
            SELECT id 
            FROM barrios 
            WHERE ST_Contains(
                ST_GeomFromText(poligono, 4326),
                ST_GeomFromText(?, 4326)
            )
            LIMIT 1
        ", [$punto]);
        
        return $barrio ? $barrio->id : null;
    }

    public function save()
    {
        // Activar estado de guardado
        $this->isSaving = true;

        try {
            DB::beginTransaction();
            
            $barrio_encontrado = $this->obtenerBarrioPorCoordenadas($this->coordenadas);

            // Buscar o crear la persona
            $persona = Persona::where('dni', $this->persona_dni)->first();
            
            if (!$persona) {
                $persona = Persona::create([
                    'dni' => $this->persona_dni,
                    'nombre' => $this->persona_nombre,
                    'apellido' => $this->persona_apellido,
                    'telefono' => $this->persona_telefono,
                    'email' => $this->persona_email,
                ]);
            } else {
                // Actualizar datos si la persona ya existe
                $persona->update([
                    'nombre' => $this->persona_nombre,
                    'apellido' => $this->persona_apellido,
                    'telefono' => $this->persona_telefono,
                    'email' => $this->persona_email,
                ]);
            }

            $domicilio = Domicilios::where('coordenadas', $this->coordenadas)->where('persona_id', $persona->id)->first();
            
            if (!$domicilio) {
                $domicilio = Domicilios::create([
                    'persona_id' => $persona->id,
                    'direccion' => $this->direccion,
                    'entre_calles' => $this->entre_calles,
                    'coordenadas' => $this->coordenadas,
                    'barrio_id' => $barrio_encontrado
                ]);
            }

            // Obtener estado inicial
            $estadoInicial = Estado::where('nombre', 'Pendiente')->first();
            if (!$estadoInicial) {
                $estadoInicial = Estado::first();
            }

            $this->area_id = Categoria::find($this->categoria_id)->area_id ?? null;
            
            // Obtener el nombre de la categoría
            $categoria = Categoria::find($this->categoria_id);
            $nombreCategoria = $categoria ? $categoria->nombre : 'Sin categoría';

            if(strlen($this->edificio_id) == 0){
                $this->edificio_id = null; // Si no se seleccionó edificio, dejar como null
            }
            
            // Crear el reclamo
            $this->reclamoCreado = Reclamo::create([
                'fecha' => now()->toDateString(),
                'descripcion' => $this->descripcion,
                'direccion' => $this->direccion,
                'entre_calles' => $this->entre_calles,
                'coordenadas' => $this->coordenadas,
                'area_id' => $this->area_id,
                'categoria_id' => $this->categoria_id,
                'edificio_id' => $this->edificio_id,
                'estado_id' => $estadoInicial->id,
                'persona_id' => $persona->id,
                'domicilio_id' => $domicilio->id,
                'barrio_id' => $barrio_encontrado,
                'usuario_id' => Auth::id() ?? 1,
                'responsable_id' => null
            ]);

            Movimiento::create([
                'reclamo_id' => $this->reclamoCreado->id,
                'tipo_movimiento_id' => 1,
                'observaciones' => 'Inicio del reclamo',
                'fecha' => now()->toDateString(),
                'usuario_id' => Auth::id() ?? 1,
                'estado_id' => 1
            ]);

            // NUEVO: Enviar email de confirmación si la persona tiene email
            if (!empty($persona->email) && filter_var($persona->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    // Cargar el reclamo con todas sus relaciones para el email
                    $reclamoCompleto = Reclamo::with(['persona', 'categoria', 'estado', 'edificio'])
                        ->find($this->reclamoCreado->id);
                    
                    \Illuminate\Support\Facades\Mail::to($persona->email)
                        ->send(new \App\Mail\ReclamoConfirmacion($reclamoCompleto));
                        
                    // Log para debugging (opcional)
                    \Illuminate\Support\Facades\Log::info('Email de confirmación enviado', [
                        'reclamo_id' => $this->reclamoCreado->id,
                        'email' => $persona->email
                    ]);
                    
                } catch (\Exception $mailException) {
                    // Si falla el envío del email, log el error pero no interrumpir el proceso
                    \Illuminate\Support\Facades\Log::error('Error enviando email de confirmación', [
                        'reclamo_id' => $this->reclamoCreado->id,
                        'email' => $persona->email,
                        'error' => $mailException->getMessage()
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');
            
            if ($this->contexto === 'externo') {
                $this->isSaving = false;
                
                // Emitir evento para mostrar éxito con redirección personalizada
                $this->dispatch('reclamo-guardado-con-redirect', [
                    'mensaje' => $this->successMessage . ' (#' . $this->reclamoCreado->id . ')',
                    'redirect_url' => $this->redirectAfterSave ?: route('reclamos')
                ]);
            }
            // Comportamiento diferente según el contexto
            elseif ($this->contexto === 'privado') {
                // Área privada: mostrar animación del botón y redirigir inmediatamente
                $this->isSaving = false;
                
                // Emitir evento local para mostrar el botón de éxito
                $this->dispatch('reclamo-creado-exitoso');
                
                // Emitir evento que manejará el toast y la redirección
                $this->dispatch('reclamo-guardado-con-redirect', [
                    'mensaje' => 'Reclamo creado exitosamente',
                    'redirect_url' => route('reclamos')
                ]);
                
                $this->isSaving = false;
                
            } else {
                // Área pública: mostrar notificación completa y redirigir al home
                $this->isSaving = false;
                
                $this->dispatch('reclamo-creado-exitoso', [
                    'id' => $this->reclamoCreado->id,
                    'fecha' => $this->reclamoCreado->fecha,
                    'nombre_completo' => $this->persona_nombre . ' ' . $this->persona_apellido,
                    'categoria' => $nombreCategoria
                ]);

                // Redirigir al home después de un delay
                $this->js('setTimeout(() => { window.location.href = "' . route('home') . '" }, 10000)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSaving = false;
            session()->flash('error', 'Error al crear el reclamo: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'descripcion', 'direccion', 'entre_calles', 'coordenadas',
            'area_id', 'categoria_id', 'persona_dni', 'persona_nombre',
            'persona_apellido', 'persona_telefono', 'persona_email'
        ]);
        $this->step = $this->showPersonaForm ? 1 : 2;
        $this->showSuccess = false;
        $this->reclamoCreado = null;
        $this->isSaving = false;
        $this->categorias = [];
        $this->edificios = [];
    }

    public function irAModificarReclamo($reclamoId)
    {
        // Cerrar el modal antes de navegar
        $this->cerrarModalDetalle();
        
        // Navegar usando Route::view con parámetros
        return $this->redirect(route('modificar-reclamo', ['reclamo' => $reclamoId]), navigate: true);
    }

    public function render()
    {
        return view('livewire.alta-reclamo');
    }
}