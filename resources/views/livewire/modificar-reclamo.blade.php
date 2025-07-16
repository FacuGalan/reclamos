<div class="mx-auto p-0">
    
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Reemplaza toda la notificación existente con esto -->
    @if($mostrarNotificacion)
        <div 
            x-data="{ visible: true }"
            x-show="visible"
            x-init="setTimeout(() => visible = false, 1500)"
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
                    @if($tipoNotificacion === 'success')
                        <p class="text-sm text-green-600">El reclamo se ha actualizado correctamente</p>
                    @else
                        <p class="text-sm text-red-600">Por favor, intente nuevamente</p>
                    @endif
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

    <!-- Formulario de modificación completo -->

    <div class="mb-2">
        <div class="flex items-center gap-3">
            <h1 class="text-3xl p-0 font-bold text-gray-800 dark:text-white">
                {{ $editable ?? true ? 'Modificar' : 'Ver' }} Reclamo #{{ $reclamo->id ?? $reclamoId }}
            </h1>
            <!-- CLAVE CORREGIDA: Incluye estado actual para actualizarse cuando cambie -->
            <livewire:estado-badge 
                :estado-id="$reclamo->estado->id" 
                size="large" 
                wire:key="estado-encabezado-{{ $reclamo->id }}-{{ $reclamo->estado->id }}" />
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-2 gap-10">
            <div>
                <!-- Sección: Datos Personales -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-200 dark:border-gray-600">
                    <svg class="w-5 h-5 inline-block mr-2 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Datos de la Persona
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            DNI *
                        </label>
                        <input 
                            type="text" 
                            wire:model="persona_dni"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el DNI">
                        @error('persona_dni') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre *
                        </label>
                        <input 
                            type="text" 
                            wire:model="persona_nombre"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el nombre">
                        @error('persona_nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Apellido *
                        </label>
                        <input 
                            type="text" 
                            wire:model="persona_apellido"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el apellido">
                        @error('persona_apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teléfono *
                        </label>
                        <input 
                            type="text" 
                            wire:model="persona_telefono"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el teléfono">
                        @error('persona_telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            wire:model="persona_email"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el email">
                        @error('persona_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div >
                <!-- Sección: Datos del Reclamo -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-200 dark:border-gray-600">
                    <svg class="w-5 h-5 inline-block mr-2 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Datos del Reclamo
                </h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Selector de categoría -->
                        <div >
                            <div x-data="{
                                    search: @entangle('categoriaBusqueda'),
                                    open: @entangle('mostrarDropdown'),
                                    selectedId: @entangle('categoria_id'),
                                    categorias: @js($categorias),
                                    
                                    get filteredCategorias() {
                                        if (this.search === '') {
                                            return this.categorias;
                                        }
                                        return this.categorias.filter(categoria => 
                                            categoria.nombre.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    },
                                    
                                    selectCategoria(categoria) {
                                        $wire.seleccionarCategoria(categoria.id);
                                    }
                                }" 
                                class="relative">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Categoría *
                                    </label>
                                    
                                    <!-- Input principal -->
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            x-model="search"
                                            @focus="open = true; $wire.mostrarTodasCategorias()"
                                            @click="open = true; $wire.mostrarTodasCategorias()"
                                            @blur="setTimeout(() => open = false, 150)"
                                            class="w-full bg-white px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                            placeholder="Busque o seleccione una categoría..."
                                            autocomplete="off">
                                        
                                        <!-- Ícono de flecha -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400 transform transition-transform" 
                                                :class="{ 'rotate-180': open }" 
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        
                                        <template x-for="categoria in filteredCategorias" :key="categoria.id">
                                            <div @click="selectCategoria(categoria)"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition-colors"
                                                :class="{ 'bg-blue-100 dark:bg-blue-900': selectedId == categoria.id }"
                                                x-text="categoria.nombre">
                                            </div>
                                        </template>
                                        
                                        <div x-show="filteredCategorias.length === 0" 
                                            class="px-3 py-2 text-gray-500 dark:text-gray-400 text-center">
                                            No se encontraron categorías que coincidan con su búsqueda
                                        </div>
                                    </div>
                                    
                                    @error('categoria_id') 
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                                    @enderror
                            </div>

                        
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Area
                            </label>
                            <input 
                                type="text"
                                readonly 
                                wire:model="area_nombre"
                                class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                placeholder="Ingrese la dirección donde ocurrió el problema">
                            @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Dirección *
                            </label>
                            <input 
                                type="text" 
                                wire:model="direccion"
                                class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                placeholder="Ingrese la dirección donde ocurrió el problema">
                            @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Entre calles
                            </label>
                            <input 
                                type="text" 
                                wire:model="entre_calles"
                                class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                placeholder="Entre qué calles se encuentra">
                            @error('entre_calles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción del reclamo *
                        </label>
                        <textarea 
                            wire:model="descripcion"
                            rows="1"
                            class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                            placeholder="Describa detalladamente el reclamo"></textarea>
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>    
        <div class="flex items-center justify-between pt-6 dark:border-gray-600" >
            <button
                wire:click="derivarReclamo"
                @if($reclamo->estado->id == 4 || !$editable) disabled @endif
                class="px-4 py-2 bg-red-500 dark:bg-red-600 text-white dark:text-gray-200 rounded-lg hover:bg-red-600 dark:hover:bg-red-500 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-400 disabled:bg-zinc-400">
                <span>
                    Derivar Reclamo
                </span>
            </button>

            @if($noAplica == 1)
                <label class="inline-flex items-center mr-4 text-red-500 font-bold">
                    Este reclamo no aplica
                </label>
            @endif

            <button 
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                x-data="{ showSuccess: false }"
                x-init="
                    $wire.on('reclamo-modificado-exitoso', () => {
                        showSuccess = true; 
                        setTimeout(() => showSuccess = false, 800)
                    })
                "
                @if($reclamo->estado->id == 4 || !$editable) disabled @endif
                class="px-8 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors font-medium cursor-pointer relative min-h-[2.5rem] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-400 disabled:bg-zinc-400">
                <!-- Estado normal -->
                <span wire:loading.remove wire:target="save" x-show="!showSuccess" class="flex items-center whitespace-nowrap">
                    Actualizar Reclamo
                </span>
                
                <!-- Estado guardando -->
                <span wire:loading wire:target="save">
                    <div  class="flex items-center whitespace-nowrap space-x-2">
                    <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Guardando...
                    </div>
                </span>

                <!-- Estado éxito -->
                <span x-show="showSuccess" class="flex items-center whitespace-nowrap">
                    ¡Reclamo Actualizado!
                </span>
            </button>
        </div>
    </div>


    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
        <div class="grid grid-cols-2 gap-10 bg-gray-50 dark:bg-gray-800 ">
            <div class="flex justify-start items-end m-6">
                <p class="text-xl font-semibold text-gray-800 dark:text-white">
               
                Historial de movimientos
               </p> 
            </div>
            <div class="flex justify-end m-6"> 
                <button 
                    wire:click="nuevoMovimiento1"
                    wire:target="nuevoMovimiento1"
                    @if($reclamo->estado->id == 4 || !$editable) disabled @endif
                    class="px-8 py-2 bg-[#4CB4DC] text-white rounded-lg hover:bg-[#31A0CD] transition-colors flex items-center font-medium cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-400 disabled:bg-zinc-400">
                    Nuevo Movimiento
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tipo de movimiento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($historial as $hist)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ \Carbon\Carbon::parse($hist->fecha)->format('d/m/Y') }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ \Carbon\Carbon::parse($hist->created_at)->format('H:i') }}
                                                            </div>
                                                        </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $hist->usuario->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $hist->tipoMovimiento->nombre }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $hist->observaciones }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <!-- CLAVE CORREGIDA: Incluye el timestamp para forzar re-renderización -->
                                <livewire:estado-badge 
                                    :estado-id="$hist->estado->id" 
                                    size="small" 
                                    wire:key="estado-hist-{{ $hist->id }}-{{ $historialTimestamp }}" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Acciones aquí si las necesitas -->
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron movimientos</p>
                                    <p class="text-sm">Agrega un nuevo movimiento para ver el historial.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal para nuevo movimiento -->
    @if($mostrarModal)
        @if($nuevoMovimiento)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Nuevo Movimiento</h2>
                    <form wire:submit.prevent="guardarMovimiento">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Movimiento</label>
                            <select wire:model.live="tipoMovimientoId" class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white">
                                <option value="">Seleccione un tipo de movimiento</option>
                                @foreach($tiposMovimiento as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipoMovimientoId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción</label>
                            <textarea wire:model="observaciones" rows="3" class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"></textarea>
                            @error('observaciones') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        @if($tipoMovimientoId == 4)
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="noAplica" class="form-checkbox h-5 w-5 text-[#77BF43] rounded focus:ring-[#77BF43]">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">El reclamo no aplica</span>
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" wire:model="notificado" class="form-checkbox h-5 w-5 text-[#77BF43] rounded focus:ring-[#77BF43]">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notificar al vecino</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-2">
                            <button type="button" 
                                wire:click="cerrarModal"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                                Guardar Movimiento          
                            </button>
                        </div>  
                    </form>
                </div>
            </div>  
        @endif
         <!-- Modal para Derivar -->
        @if($derivar)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Derivar Reclamo</h2>
                    <form wire:submit.prevent="guardarDerivacion">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Área</label>
                            <select wire:model="nuevaArea" class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white">
                                <option value="">Seleccione Área a derivar</option>
                                @foreach($areas as $area)
                                    @if($area->id == $reclamo->area_id)
                                        <option value="{{ $area->id }}" selected disabled>{{ $area->nombre }} (Actual)</option>
                                    @else
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option> 
                                    @endif
                                    
                                @endforeach
                            </select>
                            @error('nuevaArea') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción</label>
                            <textarea wire:model="observaciones" rows="3" class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"></textarea>
                            @error('observaciones') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" 
                                wire:click="cerrarModal"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                                Derivar Reclamo          
                            </button>
                        </div>  
                    </form>
                </div>
            </div>  
        @endif
    @endif

    <!-- Script para manejo de navegación automática -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('reclamo-actualizado', (event) => {
                // Después de 3 segundos, volver automáticamente a la lista
                setTimeout(() => {
                    @this.volverALista();
                }, 3000);
            });
        });
    </script>

</div>