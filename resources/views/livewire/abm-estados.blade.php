<?php

use Livewire\Volt\Component;
use App\Models\Estado;

new class extends Component {
    public $estados = [];
    public $showModal = false;
    public $showDeleteModal = false;
    public $editingEstadoId = null;
    public $selectedEstado = null;
    public $nombre = '';
    public $codigo_color = '#F3F4F6';
    public $color_texto = '';
    public $isEditing = false;
    public $isSaving = false;
    
    // Notificaciones
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    // Filtros
    public $busqueda = '';

    public function mount(): void
    {
        $this->cargarEstados();
    }

    public function cargarEstados(): void
    {
        $query = Estado::orderBy('id');

        // Aplicar filtro de búsqueda
        if ($this->busqueda) {
            $query->where('nombre', 'like', '%' . $this->busqueda . '%');
        }

        $this->estados = $query->get();
    }

    public function updatedBusqueda(): void
    {
        $this->cargarEstados();
    }

    public function updatedCodigoColor(): void
    {
        // Este método se ejecuta cuando cambia el código de color
        // No necesita hacer nada específico, solo fuerza la reactividad
    }

    public function limpiarFiltros(): void
    {
        $this->busqueda = '';
        $this->cargarEstados();
    }

    public function nuevoEstado(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editarEstado($estadoId): void
    {
        $estado = Estado::find($estadoId);
        if (!$estado) {
            $this->mostrarNotificacionError('El estado solicitado no existe.');
            return;
        }

        $this->editingEstadoId = $estadoId;
        $this->isEditing = true;
        $this->nombre = $estado->nombre;
        $this->codigo_color = $estado->codigo_color;
        $this->color_texto = $estado->color_texto ?? '';
        $this->showModal = true;
    }

    public function confirmarEliminacion($estadoId): void
    {
        $this->selectedEstado = Estado::find($estadoId);
        $this->showDeleteModal = true;
    }

    public function eliminarEstado(): void
    {
        if ($this->selectedEstado) {
            try {
                $this->selectedEstado->delete();
                $this->showDeleteModal = false;
                $this->selectedEstado = null;
                $this->cargarEstados();
                $this->mostrarNotificacionExito('Estado eliminado exitosamente');
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar el estado: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->selectedEstado = null;
    }

    public function save(): void
    {
        $this->isSaving = true;

        try {
            if ($this->isEditing) {
                // Actualizar reglas para edición
                $rules = [
                    'nombre' => 'required|string|max:255|unique:estados,nombre,' . $this->editingEstadoId,
                    'codigo_color' => 'required|string|max:7',
                    'color_texto' => 'nullable|string|max:7',
                ];
            } else {
                $rules = [
                    'nombre' => 'required|string|max:255|unique:estados,nombre',
                    'codigo_color' => 'required|string|max:7',
                    'color_texto' => 'nullable|string|max:7',
                ];
            }

            $this->validate($rules, [
                'nombre.required' => 'El nombre del estado es obligatorio',
                'nombre.unique' => 'Ya existe un estado con este nombre',
                'codigo_color.required' => 'El código de color es obligatorio',
            ]);

            if ($this->isEditing) {
                Estado::find($this->editingEstadoId)->update([
                    'nombre' => $this->nombre,
                    'codigo_color' => $this->codigo_color,
                    'color_texto' => $this->color_texto ?: null,
                ]);
                $mensaje = 'Estado actualizado exitosamente';
            } else {
                Estado::create([
                    'nombre' => $this->nombre,
                    'codigo_color' => $this->codigo_color,
                    'color_texto' => $this->color_texto ?: null,
                ]);
                $mensaje = 'Estado creado exitosamente';
            }

            $this->cargarEstados();
            $this->cerrarModal();
            $this->mostrarNotificacionExito($mensaje);

        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar el estado: ' . $e->getMessage());
        }

        $this->isSaving = false;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingEstadoId = null;
        $this->nombre = '';
        $this->codigo_color = '#F3F4F6';
        $this->color_texto = '';
        $this->isEditing = false;
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function seleccionarColor($color): void
    {
        $this->codigo_color = $color;
        // Limpiar el color de texto personalizado cuando se selecciona un predefinido
        $this->color_texto = '';
    }

    public function mostrarNotificacionExito($mensaje = 'Operación realizada exitosamente'): void
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'success';
        $this->notificacionTimestamp = microtime(true);
    }

    public function mostrarNotificacionError($mensaje): void
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'error';
        $this->notificacionTimestamp = microtime(true);
    }

    public function getColoresPredefinidos(): array
    {
        return Estado::getColoresPredefinidos();
    }
}; ?>

<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">

    <!-- Notificación flotante -->
    @if($mostrarNotificacion)
        <div 
            x-data="{ visible: true }"
            x-show="visible"
            x-init="setTimeout(() => visible = false, 3000)"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            wire:key="notification-{{ $notificacionTimestamp ?? time() }}"
            class="fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-sm
                @if($tipoNotificacion === 'success') bg-green-100 border border-green-400 text-green-700
                @else bg-red-100 border border-red-400 text-red-700 @endif">
            
            <div class="flex items-center">
                @if($tipoNotificacion === 'success')
                    <svg class="h-6 w-6 mr-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-6 w-6 mr-3 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.354 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                @endif
                
                <div>
                    <p class="font-semibold">{{ $mensajeNotificacion }}</p>
                </div>
                
                <button 
                    @click="visible = false"
                    class="ml-4 @if($tipoNotificacion === 'success') text-green-400 hover:text-green-600 @else text-red-400 hover:text-red-600 @endif">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Estados</h1>
            <p class="text-gray-600 dark:text-gray-300">Administra los estados y sus colores en el sistema</p>
        </div>
        
        <button 
            wire:click="nuevoEstado"
            class="px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center gap-2 cursor-pointer">
            Nuevo Estado
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros</h3>
            <button 
                wire:click="limpiarFiltros"
                class="px-4 py-2 bg-[#314158] hover:bg-[#4A5D76] text-white rounded-lg transition-colors cursor-pointer">
                Limpiar Filtros
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Búsqueda general -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Búsqueda</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Buscar por nombre de estado...">
            </div>
        </div>
    </div>

    <!-- Tabla de estados -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Código de Color
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Color de Texto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fecha Creación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($estados as $estado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $estado->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $estado->nombre }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $estado->codigo_color }}"></div>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-white font-mono">{{ $estado->codigo_color }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($estado->color_texto)
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $estado->color_texto }}"></div>
                                        <span class="ml-2 text-sm text-gray-900 dark:text-white font-mono">{{ $estado->color_texto }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">Automático</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($estado->created_at)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($estado->id > 5)
                                    <div class="flex items-center gap-2">
                                        <!-- Editar -->
                                        <button 
                                            wire:click="editarEstado({{ $estado->id }})"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <!-- Eliminar -->
                                        <button 
                                            wire:click="confirmarEliminacion({{ $estado->id }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                            title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron estados</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear un nuevo estado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Formulario (Crear/Editar) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ $isEditing ? 'Editar Estado' : 'Nuevo Estado' }}
                    </h2>
                    <button 
                        wire:click="cerrarModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre del Estado *
                        </label>
                        <input 
                            type="text" 
                            wire:model="nombre"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el nombre del estado">
                        @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Color de fondo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color de Fondo *
                        </label>
                        
                        <!-- Color picker y input texto -->
                        <div class="flex items-center space-x-3 mb-4">
                            <input 
                                type="color" 
                                wire:model.live="codigo_color"
                                class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input 
                                type="text" 
                                wire:model="codigo_color"
                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white font-mono"
                                placeholder="#FFFFFF">
                        </div>

                        <!-- Color de texto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color de Texto (opcional)
                        </label>
                    
                        <!-- Color picker y input texto -->
                        <div class="flex items-center space-x-3 mb-4">
                            <input 
                                type="color" 
                                wire:model.live="color_texto"
                                class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input 
                                type="text" 
                                wire:model="color_texto"
                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white font-mono"
                                placeholder="#000000 (opcional)">
                            <button 
                                type="button"
                                wire:click="$set('color_texto', '')"
                                class="px-3 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors text-sm cursor-pointer">
                                Limpiar
                            </button>
                        </div>

                        @error('color_texto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                        <!-- Colores predefinidos -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Colores predefinidos:</p>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($this->getColoresPredefinidos() as $color => $descripcion)
                                    <button 
                                        type="button"
                                        wire:click="seleccionarColor('{{ $color }}')"
                                        class="w-12 h-10 rounded border-2 hover:border-gray-400 cursor-pointer transition-all {{ $codigo_color === $color ? 'border-gray-600 ring-2 ring-blue-500' : 'border-gray-200' }}"
                                        style="background-color: {{ $color }}"
                                        title="{{ $descripcion }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @error('codigo_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>


                    <!-- Vista previa en tiempo real -->
                    @if($nombre && $codigo_color)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 m-0">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Vista previa:</p>
                            <div class="flex items-center space-x-4">
                                <!-- Badge como se verá en el sistema -->
                                <span 
                                    class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full"
                                    style="background-color: {{ $codigo_color }}; color: {{ $color_texto ?: (new \App\Models\Estado(['codigo_color' => $codigo_color]))->getColorTexto() }};">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $nombre }}
                                </span>
                                
                                <!-- Información de colores -->
                                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded border mr-2" style="background-color: {{ $codigo_color }}"></div>
                                        <span>Fondo: <span class="font-mono">{{ $codigo_color }}</span></span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded border mr-2" style="background-color: {{ $color_texto ?: (new \App\Models\Estado(['codigo_color' => $codigo_color]))->getColorTexto() }}"></div>
                                        <span>Texto: <span class="font-mono">{{ $color_texto ?: 'Automático (' . (new \App\Models\Estado(['codigo_color' => $codigo_color]))->getColorTexto() . ')' }}</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            wire:click="cerrarModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer relative">
                            
                            <!-- Estado normal -->
                            <span wire:loading.remove wire:target="save">
                                {{ $isEditing ? 'Actualizar' : 'Crear' }}
                            </span>
                            
                            <!-- Estado guardando -->
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de confirmación para eliminar -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.349 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Eliminar Estado
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            ¿Estás seguro de que deseas eliminar el estado "{{ $selectedEstado?->nombre }}"? Esta acción no se puede deshacer.
                        </p>
                        @if($selectedEstado)
                            <div class="mt-3">
                                @volt('estado-badge', ['estadoId' => $selectedEstado->id, 'showIcon' => true], key('delete-preview-'.$selectedEstado->id))
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="cerrarModalEliminacion" 
                        type="button" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button 
                        wire:click="eliminarEstado" 
                        type="button" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>