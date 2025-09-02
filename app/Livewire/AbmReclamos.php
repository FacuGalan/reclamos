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
use Illuminate\Support\Facades\Auth;

use App\Exports\GenericExport;



class AbmReclamos extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $busqueda_id = '';
    public $filtro_barrio = '';
    public $filtro_estado = '';
    public $filtro_area = '';
    public $filtro_categoria = '';
    public $filtro_fecha_desde = '';
    public $filtro_fecha_hasta = '';
    public $filtro_edificio = '';

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

    public function getReclamos($forExport = false)
    {
        $query = Reclamo::with(['persona', 'categoria', 'area', 'estado','barrio','edificio', 'usuario', 'responsable'])
            ->whereIn('area_id', $this->userAreas) // ← FILTRO PRINCIPAL: Solo reclamos de áreas del usuario
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

        // FILTRO POR CATEGORÍAS PRIVADAS

        if(Auth::user()->rol->id > 1){
            $query->whereHas('categoria', function ($q) {
                $q->where('privada', $this->ver_privada);
            });
        }

        

        $this->listaTimestamp = microtime(true);

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
    public function exportarExcel()
        {
            // 1. Obtener los datos filtrados
            $data = $this->getReclamos(true)->get();
            
            // 2. Definir encabezados
            $headings = [
                'ID',
                'Fecha',
                'Nombre',
                'Apellido',
                'DNI',
                'Teléfono',
                'Email',
                'Categoría',
                'Área',
                'Descripción',
                'Dirección',
                'Barrio',
                'Estado',
                'Usuario Creador',
                'Responsable',
                'Fecha Creación'
            ];
            
            // 3. Función de mapeo personalizada
            $mappingCallback = function ($reclamo) {
                return [
                    $reclamo->id,
                    \Carbon\Carbon::parse($reclamo->fecha)->format('d/m/Y'),
                    $reclamo->persona->nombre,
                    $reclamo->persona->apellido,
                    $reclamo->persona->dni,
                    $reclamo->persona->telefono ?? 'N/A',
                    $reclamo->persona->email ?? 'N/A',
                    $reclamo->categoria->nombre,
                    $reclamo->area->nombre,
                    $reclamo->descripcion,
                    $reclamo->direccion,
                    $reclamo->barrio->nombre ?? 'N/A',
                    $reclamo->estado->nombre,
                    $reclamo->usuario?->name ?? 'N/A',
                    $reclamo->responsable?->name ?? 'Sin asignar',
                    $reclamo->created_at->format('d/m/Y H:i'),
                ];
            };
            
            // 4. Estilo personalizado para encabezados (tu color verde)
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '77BF43'], // Tu color verde personalizado
                ],
            ];
            
            // 5. Crear y descargar la exportación
            $export = new GenericExport(
                $data,
                $headings,
                $mappingCallback,
                'Reclamos - ' . date('d-m-Y'),
                $headerStyle
            );
            
            return $export->download();
        }

    public function render()
    {
        // Solo obtener reclamos si estamos en la vista de lista
        $reclamos = $this->currentView === 'list' ? $this->getReclamos() : collect();
        
        return view('livewire.abm-reclamos', [
            'reclamos' => $reclamos
        ]);
    }
}