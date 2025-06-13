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

class AltaReclamo extends Component
{
    // Datos del reclamo
    public $descripcion = '';
    public $direccion = '';
    public $entre_calles = '';
    public $coordenadas = '';
    public $area_id = '';
    public $categoria_id = '';
    
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
    public $areas = [];
    public $categorias = [];
    
    // Props para reutilización
    public $showPersonaForm = true; // Si false, usa el usuario autenticado
    public $redirectAfterSave = null; // URL de redirección después de guardar
    public $successMessage = 'Reclamo creado exitosamente';

    protected $rules = [
        'persona_dni' => 'required|numeric|digits_between:7,11',
        'persona_nombre' => 'required|string|max:255',
        'persona_apellido' => 'required|string|max:255',
        'persona_telefono' => 'nullable|numeric|digits_between:10,15',
        'persona_email' => 'nullable|email|max:255',
        'descripcion' => 'required|string|max:1000',
        'direccion' => 'required|string|max:255',
        'entre_calles' => 'nullable|string|max:255',
        'coordenadas' => 'required|string',
        'area_id' => 'required|exists:areas,id',
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
        'direccion.required' => 'La dirección es obligatoria',
        'coordenadas.required' => 'Las coordenadas son obligatorias',
        'area_id.required' => 'Debe seleccionar un área',
        'categoria_id.required' => 'Debe seleccionar una categoría',
    ];

    public function mount()
    {
        $this->areas = Area::with('secretaria')->orderBy('nombre')->get();
        
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

    public function updatedAreaId()
    {
        $this->categoria_id = '';
        $this->categorias = Categoria::where('area_id', $this->area_id)->orderBy('nombre')->get();
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
            'area_id' => $this->rules['area_id'],
            'categoria_id' => $this->rules['categoria_id'],
        ]);
    }

    public function save()
    {
        // Validar todos los datos
        if ($this->showPersonaForm) {
            $this->validateStep1();
        }
        $this->validateStep2();

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

            // Crear o actualizar domicilio
            $domicilio = Domicilios::updateOrCreate(
                [
                    'persona_id' => $persona->id,
                    'direccion' => $this->direccion,
                ],
                [
                    'entre_calles' => $this->entre_calles,
                    'coordenadas' => $this->coordenadas,
                ]
            );

            // Obtener estado inicial (asumo que existe un estado "Pendiente" con ID 1)
            $estadoInicial = Estado::where('nombre', 'Pendiente')->first();
            if (!$estadoInicial) {
                $estadoInicial = Estado::first(); // Tomar el primer estado disponible
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
                'estado_id' => $estadoInicial->id,
                'usuario_id' => Auth::id() ?? 1, // Si no hay usuario autenticado, usar ID 1 (admin por defecto)
                'responsable_id' => Auth::id() ?? 1,
            ]);

            DB::commit();

            $this->showSuccess = true;
            
            // Emitir evento para notificar el éxito
            $this->dispatch('reclamo-creado', [
                'id' => $this->reclamoCreado->id,
                'message' => $this->successMessage
            ]);

            // Si hay URL de redirección, redirigir después de unos segundos
            if ($this->redirectAfterSave) {
                session()->flash('success', $this->successMessage);
                $this->redirect($this->redirectAfterSave, navigate: true);
            }

        } catch (\Exception $e) {
            DB::rollBack();
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
        $this->categorias = [];
    }

    public function render()
    {
        return view('livewire.alta-reclamo');
    }
}