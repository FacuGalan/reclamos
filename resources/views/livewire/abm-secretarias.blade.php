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
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Secretarías</h1>
            <p class="text-gray-600 dark:text-gray-300">Administra las secretarías del municipio</p>
        </div>
        
        <button 
            wire:click="nuevaSecretaria"
            class="px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Secretaría
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
                    placeholder="Buscar por nombre de secretaría...">
            </div>
        </div>
    </div>

    <!-- Tabla de secretarías -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Flash messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cantidad de Áreas
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
                    @forelse($secretarias as $secretaria)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $secretaria->nombre }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($secretaria->areas_count > 0) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ $secretaria->areas_count }} {{ $secretaria->areas_count == 1 ? 'área' : 'áreas' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($secretaria->created_at)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Editar -->
                                    <button 
                                        wire:click="editarSecretaria({{ $secretaria->id }})"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer"
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron secretarías</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear una nueva secretaría.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($secretarias->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $secretarias->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de Formulario (Crear/Editar) -->
@if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                    {{ $isEditing ? 'Editar Secretaría' : 'Nueva Secretaría' }}
                </h2>
                <button 
                    wire:click="cerrarModal"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- CLAVE: Agregar wire:key único para forzar re-renderización -->
            <div wire:key="form-{{ $showFormModal ? 'open' : 'closed' }}-{{ $isEditing ? 'edit' : 'create' }}">
                <form wire:submit.prevent="save" class="space-y-4">
                    
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre de la Secretaría *
                        </label>
                        <input 
                            type="text" 
                            wire:model.live="nombre"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white
                                @error('nombre') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Ingrese el nombre de la secretaría"
                            autofocus>
                        
                        <!-- MENSAJE DE ERROR MÁS VISIBLE -->
                        @error('nombre') 
                            <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-md">
                                <span class="text-red-600 text-sm font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </span>
                            </div>
                        @enderror

                        
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            wire:click="cerrarModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancelar
                        </button>
                        
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors relative">
                            
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
                            Eliminar Secretaría
                        </h3>
                    </div>
                </div>
                <div class="mb-2"> 
                    @if($selectedSecretaria && $selectedSecretaria->areas_count > 0)
                            <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    ⚠️ Esta secretaría tiene {{ $selectedSecretaria->areas_count }} área(s) asociada(s). No se puede eliminar.
                                </p>
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                ¿Estás seguro de que deseas eliminar la secretaría "{{ $selectedSecretaria?->nombre }}"?
                            </p>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                Esta acción no se puede deshacer.
                            </p>
                        @endif
                </div>
                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="cerrarModalEliminacion" 
                        type="button" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    @if(!$selectedSecretaria || $selectedSecretaria->areas_count == 0)
                        <button 
                            wire:click="eliminarSecretaria" 
                            type="button" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                            Eliminar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>