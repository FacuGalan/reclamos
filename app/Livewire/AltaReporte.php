<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reporte;
use App\Models\Persona;
use App\Models\Domicilios;
use App\Models\ReporteCategoria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AltaReporte extends Component
{
    // Estado del formulario
    public $step = 1;
    public $showSuccess = false;

    // Datos personales (opcional)
    public $incluir_datos_personales = false;
    public $persona_id;
    public $personaEncontrada = false;
    public $dni;
    public $nombre;
    public $apellido;
    public $telefono;
    public $email;

    // Datos del reporte
    public $categorias = [];
    public $categoria_id; // ID de la categoría seleccionada
    public $direccion;
    public $entre_calles;
    public $coordenadas;
    public $habitual = false; // Si es un reclamo habitual
    public $descripcion;
    public $fecha_incidente;

    // Confirmación
    public $successMessage;
    public $reporteCreado;

    // Propiedades para el mapa
    public $mostrarMapa = false;
    public $latitud = null;
    public $longitud = null;
    public $direccionCompleta = '';

    protected $listeners = [
        'confirmar-ubicacion-mapa' => 'confirmarUbicacionMapa'
    ];

    protected $rules = [
        'dni' => 'required|numeric|digits:8',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'nullable|numeric|digits_between:10,15',
        'email' => 'nullable|email|max:255',
        'categoria_id' => 'required|exists:reporte_categorias,id',
        'direccion' => 'required|string|max:255',
        'descripcion' => 'required|string|max:1000',
        'fecha_incidente' => 'nullable|date',
    ];
    

    protected $messages = [
        'dni.required' => 'El DNI es obligatorio',
        'dni.numeric' => 'El DNI debe contener solo números',
        'dni.digits' => 'El DNI debe tener 8 dígitos',
        'nombre.required' => 'El nombre es obligatorio',
        'apellido.required' => 'El apellido es obligatorio',
        'telefono.digits_between' => 'El teléfono debe tener 10 dígitos',
        'email.email' => 'Ingrese un email válido',
        'categoria_id.required' => 'Debe seleccionar una categoría',
        'categoria_id.exists' => 'La categoría seleccionada no es válida',
        'direccion.required' => 'La dirección es obligatoria',
        'coordenadas.required' => 'Dirección no validada - Use el mapa para mayor precisión',
        'descripcion.required' => 'La descripción del reclamo es obligatoria',
        'descripcion.max' => 'La descripción no puede exceder los 1000 caracteres',
    ];

    public function mount(){
        $this->fecha_incidente = date('Y-m-d');

        $this->categorias = ReporteCategoria::orderBy('nombre')->get();
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

    // DNI: Se ejecuta cuando cambia el DNI
    public function updatedDni()
    {

        // Limpiar errores previos del DNI
        $this->resetErrorBag('dni');

        
        // Solo buscar si el DNI tiene al menos 7 dígitos y es numérico
        if (strlen($this->dni) >= 7 && is_numeric($this->dni)) {
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
            $persona = Persona::where('dni', $this->dni)->first();
            
            if ($persona) {
                // Persona encontrada - completar campos automáticamente
                $this->nombre = $persona->nombre;
                $this->apellido = $persona->apellido;
                $this->telefono = $persona->telefono;
                $this->email = $persona->email;
                $this->personaEncontrada = true;
                $this->persona_id = $persona->id; // Guardar el ID de la persona
                
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
        $this->nombre = '';
        $this->apellido = '';
        $this->telefono = '';
        $this->email = '';
        $this->personaEncontrada = false;
        $this->persona_id = null;
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    private function validateStep()
    {
        if ($this->step === 1 && $this->incluir_datos_personales) {
            $this->validate([
                'dni' =>  $this->rules['dni'],
                'apellido' =>  $this->rules['apellido'],
                'nombre' => $this->rules['nombre'],
                'telefono' => $this->rules['telefono'],
                'email' => $this->rules['email'],
            ]);
        }
    
        if ($this->step === 2) {
    
            $this->validate([
                'categoria_id' => $this->rules['categoria_id'],
                'direccion' => $this->rules['direccion'],
                'descripcion' => $this->rules['descripcion'],
                'fecha_incidente' => $this->rules['fecha_incidente'],
            ]);
        }
    }

    public function save()
    {
        $this->validateStep();

        try {
            DB::beginTransaction();

            if ($this->incluir_datos_personales) {
                if (!$this->personaEncontrada) {
                    $persona = Persona::create([
                        'dni' => $this->dni,
                        'nombre' => $this->nombre,
                        'apellido' => $this->apellido,
                        'telefono' => $this->telefono,
                        'email' => $this->email,
                    ]);
                    $this->persona_id = $persona->id;
                }
            }
            
            $domicilio = Domicilios::where('coordenadas', $this->coordenadas)->where('persona_id', $this->persona_id)->first();
            
            if (!$domicilio) {
                $domicilio = Domicilios::create([
                    'persona_id' => $this->persona_id,
                    'direccion' => $this->direccion,
                    'coordenadas' => $this->coordenadas
                ]);
            }

            if (!$this->categoria_id) {
                throw new \Exception('Debe seleccionar una categoría.');
            }

            $this->reporteCreado = Reporte::create([
                'fecha' => $this->fecha_incidente,
                'persona_id' => $this->persona_id,
                'domicilio_id' => $domicilio->id,
                'coordenadas' => $this->coordenadas,
                'habitual' => $this->habitual,
                'observaciones' => $this->descripcion,
                'categoria_id' => $this->categoria_id,
            ]);

            DB::commit();

            // AQUÍ ESTÁ LA CORRECCIÓN: Obtener el nombre de la categoría
            $categoria = ReporteCategoria::find($this->categoria_id);
            $nombreCategoria = $categoria ? $categoria->nombre : 'Sin categoría';

            $this->dispatch('reporte-creado-exitoso', [
                'id' => $this->reporteCreado->id,
                'fecha' => $this->reporteCreado->fecha,
                'nombre_completo' => ($this->nombre || $this->apellido)
                    ? trim($this->nombre . ' ' . $this->apellido)
                    : 'Anónimo',
                'categoria' => $nombreCategoria  // envío el nombre, no el ID
            ]);

            // Redirigir al home después de un delay
            $this->js('setTimeout(() => { window.location.href = "' . route('home') . '" }, 10000)');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Hubo un error al guardar el reporte. Intente nuevamente: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'step',
            'incluir_datos_personales',
            'nombre',
            'telefono',
            'email',
            'direccion',
            'descripcion',
            'fecha_incidente',
            'habitual',
            'reporteCreado',
            'successMessage',
            'showSuccess',
        ]);
        $this->step = 1;
    }

    public function render()
    {
        return view('livewire.alta-reporte');
    }
}
