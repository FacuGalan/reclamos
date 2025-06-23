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
class ModificarReclamo extends Component
{
    // ID del reclamo a modificar
    public $reclamoId;
    public $reclamo;
    
    // Datos del reclamo
    public $descripcion = '';
    public $direccion = '';
    public $entre_calles = '';
    public $coordenadas = '';
    public $area_id = '';
    public $categoria_id = '';
    public $estado_id = '';
    
    // Datos de la persona
    public $persona_dni = '';
    public $persona_nombre = '';
    public $persona_apellido = '';
    public $persona_telefono = '';
    public $persona_email = '';
    
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
        'coordenadas' => 'required|string',
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
        'coordenadas.required' => 'Las coordenadas son obligatorias',
        'categoria_id.required' => 'Debe seleccionar una categoría',
        'estado_id.required' => 'Debe seleccionar un estado',
    ];

    public function mount($reclamoId)
    {
        $this->reclamoId = $reclamoId;
        
        // Cargar datos para los selects
        $this->categorias = Categoria::orderBy('nombre')->get();
        $this->categoriasFiltradas = $this->categorias;
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::orderBy('nombre')->get();
        
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

    public function save()
    {
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

            // Emitir evento para notificar el éxito
            $this->dispatch('reclamo-actualizado', [
                'id' => $this->reclamo->id,
                'message' => 'Reclamo actualizado exitosamente'
            ]);

            // Emitir evento para cerrar modal si se usa desde el ABM
            $this->dispatch('reclamo-saved');

            $this->showSuccess = true;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el reclamo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.modificar-reclamo');
    }
}