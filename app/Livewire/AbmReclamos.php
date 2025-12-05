<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reclamo;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Estado;
use App\Models\Barrio;
use App\Models\Edificio;
use App\Models\User;
use App\Models\Cuadrilla;
use App\Models\ModeloExportacionReclamo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Exports\GenericExport;



class AbmReclamos extends Component
{
    use WithPagination;

    // Propiedades para filtros
    #[Session]
    public $busqueda = '';
    #[Session]
    public $busqueda_id = '';
    #[Session]
    public $filtro_barrio = '';
    #[Session]
    public $filtro_estado = '';
    #[Session]
    public $filtro_area = '';
    #[Session]
    public $filtro_categoria = '';
    #[Session]
    public $filtro_fecha_desde = '';
    #[Session]
    public $filtro_fecha_hasta = '';
    #[Session]
    public $filtro_edificio = '';
    #[Session]
    public $filtro_usuario = '';
    #[Session]
    public $filtro_responsable = '';
    #[Session]
    public $filtro_cuadrilla = '';
    #[Session]
    public $filtro_urgente = '';

    public $filtrosActivos = 0; // Contador de filtros activos

    // Contadores
    public $contadorTotales = 0;
    public $contadorSinProcesar = 0;
    public $contadorUrgentes = 0;

    // Propiedades para navegación entre vistas
    public $currentView = 'list'; // 'list', 'create', 'edit'
    public $reclamoEditable = true; // Para saber si se está editando un reclamo
    public $selectedReclamoId = null;
    public $showDeleteModal = false;
    public $selectedReclamo = null;
    public $reclamoInterno = false; // Para saber si es un reclamo interno o externo
    
    // Datos para los selects (filtrados por áreas del usuario)
    public $estados = [];
    public $barrios = [];
    public $areas = [];
    public $categorias = [];
    public $edificios = [];
    public $usuarios = [];
    public $cuadrillas = [];

    public $listaTimestamp; // NUEVO: para forzar re-renderización

    // Áreas del usuario logueado
    public $userAreas = [];
    public $ver_privada = false; // Para filtrar categorías privadas

    // ← ESTA ES LA CLAVE: agregar currentView y selectedReclamoId al queryString
    protected $queryString = [
        'busqueda' => ['except' => ''],
        'busqueda_id' => ['except' => ''],
        'filtro_barrio' => ['except' => ''],
        'filtro_estado' => ['except' => ''],
        'filtro_area' => ['except' => ''],
        'filtro_categoria' => ['except' => ''],
        'filtro_fecha_desde' => ['except' => ''],
        'filtro_fecha_hasta' => ['except' => ''],
        'filtro_edificio' => ['except' => ''],
        'filtro_usuario' => ['except' => ''],
        'filtro_responsable' => ['except' => ''],
        'filtro_cuadrilla' => ['except' => ''],
        'currentView' => ['except' => 'list'],              // ← AGREGAR ESTO
        'selectedReclamoId' => ['except' => null, 'as' => 'reclamo'], // ← AGREGAR ESTO
    ];

    protected $listeners = [
        'reclamo-saved' => 'volverALista',
        'reclamo-deleted' => 'volverALista',
        'reclamo-actualizado' => 'volverAListaConDelay',
    ];

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();
        $this->ver_privada = Auth::user()->ver_privada; // Verificar si el usuario puede ver categorías privadas

        $this->listaTimestamp = microtime(true); // Inicializar timestamp

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = Area::pluck('id')->toArray();
        }

        // Cargar datos filtrados por las áreas del usuario
        $this->barrios = Barrio::orderBy('nombre')->get();
        $this->estados = Estado::orderBy('nombre')->get();
        $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        $this->categorias = Categoria::whereIn('area_id', $this->userAreas)->where('privada', $this->ver_privada)->orderBy('nombre')->get();
        $this->edificios = Edificio::orderBy('nombre')->get();
        $this->usuarios = User::getUsuariosDeAreas();
        $this->cuadrillas = Cuadrilla::whereIn('area_id', $this->userAreas)->orderBy('nombre')->get();

        // Validación: Si está en modo edit, verificar que el reclamo existe y pertenece a las áreas del usuario
        if ($this->currentView === 'edit' && $this->selectedReclamoId) {
            $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($this->selectedReclamoId);
            if (!$reclamo) {
                // Si el reclamo no existe o no pertenece a las áreas del usuario, volver a la lista
                $this->currentView = 'list';
                $this->selectedReclamoId = null;
                session()->flash('error', 'El reclamo solicitado no existe o no tienes permisos para acceder a él.');
            }
        }
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroBarrio()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }


    public function updatingFiltroCategoria()
    {
        $this->resetPage();
    }

    public function updatingFiltroEdificio()
    {
        $this->resetPage();
    }

    public function updatedFiltroArea()
    {
        // Actualizar las categorías cuando cambia el área
        if ($this->filtro_area) {
            $this->categorias = Categoria::where('area_id', $this->filtro_area)
                ->where('privada', $this->ver_privada)
                ->orderBy('nombre')
                ->get();
        } else {
            // Si no hay área seleccionada, mostrar todas las categorías permitidas
            $this->categorias = Categoria::whereIn('area_id', $this->userAreas)
                ->where('privada', $this->ver_privada)
                ->orderBy('nombre')
                ->get();
        }

        // Resetear el filtro de categoría al cambiar el área
        $this->filtro_categoria = '';

    }

    public function updatedFiltroUsuario()
    {
        $this->resetPage();
    }

    public function updatedFiltroResponsable()
    {
        $this->resetPage();
    }

    public function updatedFiltroCuadrilla()
    {
        $this->resetPage();
    }

    public function getReclamos($forExport = false)
    {
        $query = Reclamo::with(['persona', 'categoria', 'area', 'estado','barrio','edificio', 'usuario', 'responsable'])
            ->whereIn('area_id', $this->userAreas) // ← FILTRO PRINCIPAL: Solo reclamos de áreas del usuario
            ->orderByRaw('CASE WHEN estado_id = 6 THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros adicionales
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('descripcion', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('direccion', 'like', '%' . $this->busqueda . '%')
                  ->orWhereHas('persona', function($subQ) {
                      $subQ->where('nombre', 'like', '%' . $this->busqueda . '%')
                           ->orWhere('apellido', 'like', '%' . $this->busqueda . '%')
                           ->orWhere('dni', 'like', '%' . $this->busqueda . '%');
                  });
            });
        }

        // Aplicar filtro id
        if ($this->busqueda_id) {
            $query->where('id', $this->busqueda_id);
        }

        if ($this->filtro_barrio) {
            $query->where('barrio_id', $this->filtro_barrio);
        }

        if ($this->filtro_estado) {
            $query->where('estado_id', $this->filtro_estado);
        }else{
            $query->whereNot('estado_id', 5)->whereNot('estado_id', 4); // Excluir estados "Cancelado" y "Finalizado"
        }

        if ($this->filtro_area) {
            // Verificar que el área filtrada esté dentro de las áreas permitidas del usuario
            if (in_array($this->filtro_area, $this->userAreas)) {
                $query->where('area_id', $this->filtro_area);
            }
        }

        if ($this->filtro_categoria) {
            // Verificar que la categoría pertenezca a las áreas permitidas del usuario
            $categoria = Categoria::whereIn('area_id', $this->userAreas)->find($this->filtro_categoria);
            if ($categoria) {
                $query->where('categoria_id', $this->filtro_categoria);
            }
        }

        if ($this->filtro_fecha_desde) {
            $query->where('fecha', '>=', $this->filtro_fecha_desde);
        }

        if ($this->filtro_fecha_hasta) {
            $query->where('fecha', '<=', $this->filtro_fecha_hasta);
        }

        if ($this->filtro_edificio) {
            $query->where('edificio_id', $this->filtro_edificio);
        }

        if ($this->filtro_usuario) {
            $query->where('usuario_id', $this->filtro_usuario);
        }

        if ($this->filtro_responsable) {
            $query->where('responsable_id', $this->filtro_responsable);
        }

        if ($this->filtro_cuadrilla) {
            // Filtrar reclamos cuya categoría pertenece a la cuadrilla seleccionada
            $query->whereHas('categoria', function ($q) {
                $q->where('cuadrilla_id', $this->filtro_cuadrilla);
            });
        }

        if ($this->filtro_urgente !== '') {
            $query->whereHas('categoria', function($q) {
                $q->where('urgente', $this->filtro_urgente);
            });
        }

        // FILTRO POR CATEGORÍAS PRIVADAS

        if(Auth::user()->rol->id > 1){
            $query->whereHas('categoria', function ($q) {
                $q->where('privada', $this->ver_privada);
            });
        }

        

        $this->listaTimestamp = microtime(true);

        $this->contadorTotales = $query->count();
        $this->contadorSinProcesar = (clone $query)->where('responsable_id', null)->count();

        // Contar urgentes (sin el filtro de urgente aplicado, excluyendo finalizados y cancelados)
        $queryUrgentes = Reclamo::whereIn('area_id', $this->userAreas)
            ->whereNot('estado_id', 5)
            ->whereNot('estado_id', 4)
            ->whereHas('categoria', function($q) {
                $q->where('urgente', true);
                if(Auth::user()->rol->id > 1){
                    $q->where('privada', $this->ver_privada);
                }
            });
        $this->contadorUrgentes = $queryUrgentes->count();

        // Condicional según el parámetro
        if ($forExport) {
            return $query; // Devuelve Collection para exportar
        } else {
            return $query->paginate(15); // Devuelve LengthAwarePaginator para la vista
        }
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->busqueda_id = '';
        $this->filtro_barrio = '';
        $this->filtro_estado = '';
        $this->filtro_area = '';
        $this->filtro_categoria = '';
        $this->filtro_fecha_desde = '';
        $this->filtro_fecha_hasta = '';
        $this->filtro_edificio = '';
        $this->filtro_usuario = '';
        $this->filtro_responsable = '';
        $this->filtro_cuadrilla = '';
        $this->filtro_urgente = '';

        $this->resetPage();
    }

    public function filtrarUrgentes()
    {
        // Limpiar todos los filtros
        $this->busqueda = '';
        $this->busqueda_id = '';
        $this->filtro_barrio = '';
        $this->filtro_estado = '';
        $this->filtro_area = '';
        $this->filtro_categoria = '';
        $this->filtro_fecha_desde = '';
        $this->filtro_fecha_hasta = '';
        $this->filtro_edificio = '';
        $this->filtro_usuario = '';
        $this->filtro_responsable = '';
        $this->filtro_cuadrilla = '';

        // Aplicar solo el filtro de urgentes
        $this->filtro_urgente = '1';

        $this->resetPage();
    }

    public function editarReclamo($reclamoId,$edita)
    {
        // Validar que el reclamo existe y pertenece a las áreas del usuario
        $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($reclamoId);
        if (!$reclamo) {
            session()->flash('error', 'El reclamo solicitado no existe o no tienes permisos para acceder a él.');
            return;
        }

        // Redirigir a la ruta de modificar reclamo
        return $this->redirect(route('modificar-reclamo', ['reclamo' => $reclamoId]), navigate: true);
    }

    public function volverALista()
    {
        $this->currentView = 'list';
        $this->selectedReclamoId = null;
        $this->showDeleteModal = false;
        $this->selectedReclamo = null;
    }

    public function volverAListaConDelay()
    {
        // Esperar un poco para mostrar el mensaje de éxito y luego volver
        $this->dispatch('delay-return-to-list');
    }

    public function verReclamo($reclamoId)
    {
        // Verificar permisos antes de mostrar el detalle
        $reclamo = Reclamo::with(['persona', 'categoria', 'area', 'estado', 'usuario', 'responsable', 'domicilio', 'movimientos.tipoMovimiento', 'movimientos.estado', 'movimientos.usuario'])
            ->whereIn('area_id', $this->userAreas)
            ->find($reclamoId);
            
        if (!$reclamo) {
            session()->flash('error', 'No tienes permisos para ver este reclamo.');
            return;
        }
        
        $this->selectedReclamo = $reclamo;
        $this->dispatch('mostrar-detalle-reclamo', ['reclamo' => $this->selectedReclamo]);
    }

    public function confirmarEliminacion($reclamoId)
    {
        // Verificar permisos antes de eliminar
        $reclamo = Reclamo::whereIn('area_id', $this->userAreas)->find($reclamoId);
        if (!$reclamo) {
            session()->flash('error', 'No tienes permisos para eliminar este reclamo.');
            return;
        }
        
        $this->selectedReclamo = $reclamo;
        $this->showDeleteModal = true;
    }

    public function eliminarReclamo()
    {
        if ($this->selectedReclamo) {
            // Verificar una vez más que el usuario tiene permisos
            if (in_array($this->selectedReclamo->area_id, $this->userAreas)) {
                //$this->selectedReclamo->delete();
                $this->selectedReclamo->update([
                    'estado_id' => 5, // Asumiendo que el estado 5 es "Cancelado"
                    'responsable_id' => Auth::id(), // Asignar el usuario que elimina
                ]);
                $this->showDeleteModal = false;
                $this->selectedReclamo = null;

                $this->dispatch('nuevo-reclamo-detectado')->to('contador-notificaciones-reclamos');
                
                session()->flash('success', 'Reclamo eliminado exitosamente.');
                $this->dispatch('reclamo-deleted');
            } else {
                session()->flash('error', 'No tienes permisos para eliminar este reclamo.');
                $this->showDeleteModal = false;
                $this->selectedReclamo = null;
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedReclamo = null;
    }

    // Método para obtener información de áreas del usuario (útil para debugging)
    public function getUserAreasInfo()
    {
        return [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'areas_count' => count($this->userAreas),
            'areas_names' => Area::whereIn('id', $this->userAreas)->pluck('nombre')->toArray(),
        ];
    }

    // Método para exportar a Excel
    public function exportarExcel($modeloId = null)
    {
        // 1. Obtener los datos filtrados
        $data = $this->getReclamos(true)->get();

        // Definir mapeo completo de campos disponibles
        $camposDisponibles = [
            'id' => [
                'etiqueta' => 'ID',
                'valor' => fn($r) => $r->id
            ],
            'fecha' => [
                'etiqueta' => 'Fecha',
                'valor' => fn($r) => \Carbon\Carbon::parse($r->fecha)->format('d/m/Y')
            ],
            'nombre_persona' => [
                'etiqueta' => 'Nombre',
                'valor' => fn($r) => $r->persona->nombre
            ],
            'apellido_persona' => [
                'etiqueta' => 'Apellido',
                'valor' => fn($r) => $r->persona->apellido
            ],
            'dni' => [
                'etiqueta' => 'DNI',
                'valor' => fn($r) => $r->persona->dni
            ],
            'telefono' => [
                'etiqueta' => 'Teléfono',
                'valor' => fn($r) => $r->persona->telefono ?? 'N/A'
            ],
            'email' => [
                'etiqueta' => 'Email',
                'valor' => fn($r) => $r->persona->email ?? 'N/A'
            ],
            'categoria' => [
                'etiqueta' => 'Categoría',
                'valor' => fn($r) => $r->categoria->nombre
            ],
            'area' => [
                'etiqueta' => 'Área',
                'valor' => fn($r) => $r->area->nombre
            ],
            'numero_tranquera' => [
                'etiqueta' => 'Tranquera',
                'valor' => fn($r) => $r->numero_tranquera ?? 'N/A'
            ],
            'direccion' => [
                'etiqueta' => 'Dirección',
                'valor' => fn($r) => $r->direccion
            ],
            'entre_calles' => [
                'etiqueta' => 'Entre calles',
                'valor' => fn($r) => $r->entre_calles ?? 'N/A'
            ],
            'direccion_rural' => [
                'etiqueta' => 'Aclaración Dirección',
                'valor' => fn($r) => $r->direccion_rural
            ],
            'barrio' => [
                'etiqueta' => 'Barrio',
                'valor' => fn($r) => $r->barrio->nombre ?? 'N/A'
            ],
            'estado' => [
                'etiqueta' => 'Estado',
                'valor' => fn($r) => $r->estado->nombre
            ],
            'usuario_creador' => [
                'etiqueta' => 'Usuario Creador',
                'valor' => fn($r) => $r->usuario?->name ?? 'N/A'
            ],
            'responsable' => [
                'etiqueta' => 'Responsable',
                'valor' => fn($r) => $r->responsable?->name ?? 'Sin asignar'
            ],
            'fecha_creacion' => [
                'etiqueta' => 'Fecha Creación',
                'valor' => fn($r) => $r->created_at->format('d/m/Y H:i')
            ],
            'descripcion' => [
                'etiqueta' => 'Descripción',
                'valor' => fn($r) => $r->descripcion
            ],
        ];

        // 2. Determinar qué campos exportar
        if ($modeloId) {
            $modelo = ModeloExportacionReclamo::findOrFail($modeloId);
            $camposAExportar = $modelo->campos;
            $tituloExport = $modelo->nombre . ' - ' . date('d-m-Y');
        } else {
            // Exportar todos los campos (comportamiento por defecto)
            $camposAExportar = array_keys($camposDisponibles);
            $tituloExport = 'Reclamos - ' . date('d-m-Y');
        }

        // 3. Construir encabezados dinámicamente
        $headings = [];
        foreach ($camposAExportar as $campo) {
            if (isset($camposDisponibles[$campo])) {
                $headings[] = $camposDisponibles[$campo]['etiqueta'];
            }
        }

        // 4. Función de mapeo personalizada dinámica
        $mappingCallback = function ($reclamo) use ($camposAExportar, $camposDisponibles) {
            $fila = [];
            foreach ($camposAExportar as $campo) {
                if (isset($camposDisponibles[$campo])) {
                    $fila[] = $camposDisponibles[$campo]['valor']($reclamo);
                }
            }
            return $fila;
        };

        // 5. Estilo personalizado para encabezados (tu color verde)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '77BF43'], // Tu color verde personalizado
            ],
        ];

        // 6. Crear y descargar la exportación
        $export = new GenericExport(
            $data,
            $headings,
            $mappingCallback,
            $tituloExport,
            $headerStyle
        );

        return $export->download();
    }

    public function contarFiltrosActivos()
    {
        $filtros = [
            $this->busqueda,
            $this->busqueda_id,
            $this->filtro_barrio,
            $this->filtro_estado,
            $this->filtro_area,
            $this->filtro_categoria,
            $this->filtro_fecha_desde,
            $this->filtro_fecha_hasta,
            $this->filtro_edificio,
            $this->filtro_usuario,
            $this->filtro_responsable,
            $this->filtro_cuadrilla,
            $this->filtro_urgente,
        ];

        $this->filtrosActivos = collect($filtros)->filter(function($valor) {
            return !empty($valor);
        })->count();
    }

    public function render()
    {
        // Solo obtener reclamos si estamos en la vista de lista
        $reclamos = $this->currentView === 'list' ? $this->getReclamos() : collect();

        $this->contarFiltrosActivos();

        // Obtener modelos de exportación disponibles para el área del usuario
        $userAreas = Auth::user()->areas;
        if ($userAreas->isEmpty()) {
            // Si el usuario no tiene áreas, mostrar todos los modelos
            $modelosExportacion = ModeloExportacionReclamo::orderBy('nombre')->get();
        } else {
            $modelosExportacion = ModeloExportacionReclamo::whereIn('area_id', $userAreas->pluck('id'))
                ->orderBy('nombre')
                ->get();
        }

        return view('livewire.abm-reclamos', [
            'reclamos' => $reclamos,
            'modelosExportacion' => $modelosExportacion
        ]);
    }
}