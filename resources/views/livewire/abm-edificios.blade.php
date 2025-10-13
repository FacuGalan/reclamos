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
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Edificios</h1>
            <p class="text-gray-600 dark:text-gray-300">Administra los edificios y sus áreas</p>
        </div>
        
        <button 
            wire:click="nuevoEdificio"
            class="px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center gap-2 cursor-pointer">
            Nuevo Edificio
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
            <!-- Búsqueda -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Búsqueda</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Buscar por nombre...">
            </div>

        </div>
    </div>

    <!-- Tabla de edificios -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($edificios as $e)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">#{{ $e->id }}</td>
                            <td class="px-6 py-4">{{ $e->nombre }}</td>
                            <td class="px-6 py-4">{{ $e->direccion }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Botón Editar -->
                                    <button 
                                        wire:click="editarEdificio({{ $e->id }})"
                                        class="ml-4 text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <p class="text-lg font-medium">No se encontraron edificios</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear un nuevo edificio.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($edificios->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $edificios->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Crear/Editar -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ $isEditing ? 'Editar Edificio' : 'Nuevo Edificio' }}
                    </h2>
                    <button 
                        wire:click="cerrarModal"
                        type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre *</label>
                        <input 
                            type="text" 
                            wire:model.live="nombre"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white">
                        @error('nombre') <div class="mt-2 text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dirección *</label>
                        <input 
                            type="text"
                            wire:model.live="direccion"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese la dirección">
                        @error('direccion') <div class="mt-2 text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="cerrarModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] cursor-pointer">{{ $isEditing ? 'Actualizar' : 'Crear' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Confirmación Eliminar -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Eliminar Edificio</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">¿Estás seguro de que deseas eliminar el edificio "{{ $selectedEdificio?->nombre }}"?</p>
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancelar</button>
                    <button wire:click="eliminarEdificio" type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

</div>