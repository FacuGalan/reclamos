<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Estado;
use App\Models\Persona;
use App\Models\Domicilios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @property Collection $categorias
 * @property Collection $categoriasFiltradas
 */

class AltaReclamo extends Component
{
    // Datos del reclamo
    public $descripcion = '';
    public $direccion = '';
    public $entre_calles = '';
    public $coordenadas = '';
    public $area_id = '';
    public $categoria_id = '';
    public $userAreas = []; // Áreas del usuario autenticado
    
    // Datos de la persona
    public $persona_dni = '';
    public $persona_nombre = '';
    public $persona_apellido = '';
    public $persona_telefono = '';
    public $persona_email = '';
    
    // Control de flujo
    public $step = 1; // 1: datos persona, 2: datos reclamo, 3: confirmación
    public $showSuccess = false;
    public $reclamoCreado = null;
    
    // Datos para selects
    public $categorias = [];
    public $categoriasFiltradas = [];
    
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

    // Nueva propiedad para el estado de guardado
    public $isSaving = false;

    protected $rules = [
        'persona_dni' => 'required|numeric|digits_between:7,11',
        'persona_nombre' => 'required|string|max:255',
        'persona_apellido' => 'required|string|max:255',
        'persona_telefono' => 'required|numeric|digits_between:10,15',
        'persona_email' => 'nullable|email|max:255',
        'descripcion' => 'required|string|max:1000',
        'direccion' => 'nullable|string|max:255',
        'entre_calles' => 'nullable|string|max:255',
        'coordenadas' => 'nullable|string',
        'categoria_id' => 'required|exists:categorias,id',
    ];

    protected $messages = [
        'persona_dni.required' => 'El DNI es obligatorio pone uno bueno',
        'persona_dni.numeric' => 'El DNI debe contener solo números',
        'persona_dni.digits_between' => 'El DNI debe tener entre 7 y 11 dígitos',
        'persona_nombre.required' => 'El nombre es obligatorio',
        'persona_apellido.required' => 'El apellido es obligatorio',
        'persona_telefono.digits_between' => 'El teléfono debe tener entre 10 y 15 dígitos',
        'persona_email.email' => 'Ingrese un email válido',
        'descripcion.required' => 'La descripción del reclamo es obligatoria',
        'descripcion.max' => 'La descripción no puede exceder los 1000 caracteres',
        'categoria_id.required' => 'Debe seleccionar una categoría',
    ];

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        $this->categorias = Categoria::where('privada', $this->isPrivateArea)
                                    ->whereIn('area_id', $this->userAreas)
                                    ->orderBy('privada')
                                    ->orderBy('nombre')
                                    ->get();

        $this->categoriasFiltradas = $this->categorias;
        
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
                
                // Emitir evento de JavaScript para mostrar notificación
                $this->dispatch('persona-encontrada', [
                    'mensaje' => 'Persona encontrada en el sistema. Datos completados automáticamente.'
                ]);
                
            } else {
                // Persona no encontrada - limpiar campos
                $this->limpiarDatosPersona();
                $this->personaEncontrada = false;
            }
        } catch (\Exception $e) {
            // En caso de error, limpiar campos
            $this->limpiarDatosPersona();
            $this->personaEncontrada = false;
        }
    }

    // DNI: Limpiar datos de persona
    private function limpiarDatosPersona()
    {
        $this->persona_nombre = '';
        $this->persona_apellido = '';
        $this->persona_telefono = '';
        $this->persona_email = '';
        $this->personaEncontrada = false;
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
        $this->validate([
            'descripcion' => $this->rules['descripcion'],
            'direccion' => $this->rules['direccion'],
            'entre_calles' => $this->rules['entre_calles'],
            'coordenadas' => $this->rules['coordenadas'],
            'categoria_id' => $this->rules['categoria_id'],
        ]);
    }

    public function save()
    {
        // Activar estado de guardado
        $this->isSaving = true;

        try {
            DB::beginTransaction();

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

            // Crear el reclamo
            $this->reclamoCreado = Reclamo::create([
                'fecha' => now()->toDateString(),
                'descripcion' => $this->descripcion,
                'direccion' => $this->direccion,
                'entre_calles' => $this->entre_calles,
                'coordenadas' => $this->coordenadas,
                'area_id' => $this->area_id,
                'categoria_id' => $this->categoria_id,
                'estado_id' => $estadoInicial->id,
                'persona_id' => $persona->id,
                'domicilio_id' => $domicilio->id,
                'usuario_id' => Auth::id() ?? 1,
                'responsable_id' => Auth::id() ?? 1,
            ]);

            DB::commit();
            
            // Comportamiento diferente según el contexto
            if (Auth::check()) {
                // Área privada: mostrar animación del botón y redirigir inmediatamente
                $this->isSaving = false;
                
                // Emitir evento local para mostrar el botón de éxito
                $this->dispatch('reclamo-creado-exitoso');
                
                // Volver al ABM con un mensaje de éxito que se mostrará allí
                session()->flash('reclamo_creado', 'Reclamo creado exitosamente');
                
                // Redirección inmediata sin delay
                $this->redirect(route('reclamos'), navigate: true);
                
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
    }

    public function render()
    {
        return view('livewire.alta-reclamo');
    }
}