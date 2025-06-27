<div class="mx-auto p-0">
    
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de modificación completo -->
    <div class="mb-2">
        <div class="flex items-center gap-3">
            <h1 class="text-3xl p-0 font-bold text-gray-800 dark:text-white">
                Modificar Reclamo #{{ $reclamo->id ?? $reclamoId }}
            </h1>
            <span class="px-2 py-1 text-lg font-semibold rounded-full
                    @if($reclamo->estado->nombre == 'Pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($reclamo->estado->nombre == 'En Proceso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($reclamo->estado->nombre == 'Resuelto') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($reclamo->estado->nombre == 'Cerrado') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ $reclamo->estado->nombre }}
                </span>
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
        <div class="flex justify-end items-center pt-6 dark:border-gray-600" >
            <button 
                wire:click="save"
                class="px-8 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center font-medium cursor-pointer">

                Actualizar Reclamo
            </button>
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 my-2  p-6">
        <div class="flex justify-end mb-4"> 
            <button 
                wire:click="nuevoMovimiento1"
                class="px-8 py-2 bg-[#91D5E2] text-white rounded-lg hover:bg-[#5AB1BF] transition-colors flex items-center font-medium cursor-pointer">
                Nuevo Movimiento
            </button>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($hist->fecha)->format('d/m/Y') }}
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
                                {{ $hist->estado->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                    

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
                                    <p class="text-lg font-medium">No se encontraron reclamos</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear un nuevo reclamo.</p>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Nuevo Movimiento</h2>
                <form wire:submit.prevent="guardarMovimiento">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Movimiento</label>
                        <select wire:model="tipoMovimientoId" class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white">
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
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                            wire:click="cerrarModal"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors">
                            Guardar Movimiento          
                        </button>
                    </div>  
                </form>
            </div>
        </div>  
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