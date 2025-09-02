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
                
                <a href="#" 
                onclick="event.preventDefault(); 
                        @this.call('prepararDatosImpresion').then(() => {
                            window.open('{{ route('orden.imprimir') }}', '_blank', 'width=900,height=1200'); 
                        }); 
                        return false;"
                class="w-full md:w-auto px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white dark:text-gray-200 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Imprimir Orden</span>
                </a>

        </div>
    </div>

    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid lg:grid-cols-2 gap-8">
            <div>
                <!-- Sección: Datos Personales -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-200 dark:border-gray-600">
                    <svg class="w-5 h-5 inline-block mr-2 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Datos de la Persona
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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
                
                <div class="space-y-2">
                    <div class="grid grid-cols-1 {{ $isPrivateArea ? '' : 'md:grid-cols-2' }} gap-2">
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

                        <div {{ $isPrivateArea ? 'hidden' : '' }} >
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Area
                            </label>
                            <input 
                                type="text"
                                readonly 
                                wire:model="area_nombre"
                                class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                placeholder="Ingrese la dirección donde ocurrió el problema">
                            @error('area_nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2">
                        @if(!$reclamo->categoria->privada)
                            
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Dirección *
                                    </label>
                                    <div class="flex gap-2">
                                        <div class="flex-1 relative">
                                            <input 
                                                type="text" 
                                                id="direccion-autocomplete"
                                                wire:model="direccion"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-white"
                                                placeholder="Busque y seleccione una dirección..."
                                                autocomplete="off">
                                            
                                            <!-- Indicador de validación -->
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <div id="direccion-status" class="hidden">
                                                    <!-- Icono de éxito -->
                                                    <svg id="direccion-success" class="w-5 h-5 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <!-- Icono de error -->
                                                    <svg id="direccion-error" class="w-5 h-5 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <button 
                                            type="button"
                                            wire:click="abrirMapa"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer flex items-center"
                                            title="Seleccionar en el mapa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('coordenadas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    
                                    <!-- Mensaje de estado de la dirección -->
                                    <div id="direccion-mensaje" class="mt-2 text-sm hidden">
                                        <div id="direccion-validada" class="text-green-600 dark:text-green-400 flex items-center hidden">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Dirección validada</span>
                                        </div>
                                        <div id="direccion-no-validada" class="text-amber-600 dark:text-amber-400 flex items-center hidden">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span>Dirección no validada - Use el mapa para mayor precisión</span>
                                        </div>
                                    </div>
                                    @if($coordenadas && $mostrarCalleSeleccionada)
                                        <div>
                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mt-2">
                                                <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Ubicación confirmada: {{ $direccionCompleta ?: $direccion }} 
                                                </p>
                                            </div>
                                        </div>
                                    @endif
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
                            
                        @else 
                                <div x-data="{
                                    search: @entangle('edificioBusqueda'),
                                    open: @entangle('mostrarDropdownEdificios'),
                                    selectedId: @entangle('edificio_id'),
                                    edificios: @js($edificios),

                                    get filteredEdificios() {
                                        if (this.search === '') {
                                            return this.edificios;
                                        }
                                        return this.edificios.filter(edificio => 
                                            edificio.nombre.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    },

                                    selectEdificio(edificio) {
                                        $wire.seleccionarEdificio(edificio.id);
                                    }
                                }" 
                                class="relative">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Edificio *
                                    </label>
                                    
                                    <!-- Input principal -->
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            x-model="search"
                                            @focus="open = true; $wire.mostrarTodosEdificios()"
                                            @click="open = true; $wire.mostrarTodosEdificios()"
                                            @blur="setTimeout(() => open = false, 150)"
                                            class="w-full bg-white px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                            placeholder="Busque o seleccione un edificio..."
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

                                        <template x-for="edificio in filteredEdificios" :key="edificio.id">
                                            <div @click="selectEdificio(edificio)"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition-colors"
                                                :class="{ 'bg-blue-100 dark:bg-blue-900': selectedId == edificio.id }"
                                                x-text="edificio.nombre">
                                            </div>
                                        </template>
                                        
                                        <div x-show="filteredEdificios.length === 0" 
                                            class="px-3 py-2 text-gray-500 dark:text-gray-400 text-center">
                                            No se encontraron edificios que coincidan con su búsqueda
                                        </div>
                                    </div>
                                    
                                    @error('edificio_id') 
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                                    @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Dirección
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.live="direccion"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-white"
                                    placeholder="Entre qué calles se encuentra">
                                @error('direccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>    

        <div class="grid grid-cols-1 gap-10 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descripción del reclamo *
                </label>
                <textarea 
                    wire:model="descripcion"
                    rows="2"
                    class="w-full bg-white px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                    placeholder="Describa detalladamente el reclamo"></textarea>
                @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between pt-6 dark:border-gray-600" >
            @if(Auth::user()->rol->lReclamosDeriva && !$reclamo->categoria->privada)
                <button
                    wire:click="derivarReclamo"
                    @if($reclamo->estado->id == 4 || !$editable) disabled @endif
                    class="w-full md:w-auto px-4 py-2 bg-red-500 dark:bg-red-600 text-white dark:text-gray-200 rounded-lg hover:bg-red-600 dark:hover:bg-red-500 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-400 disabled:bg-zinc-400">
                    <span>
                        Derivar Reclamo
                    </span>
                </button>
            @endif

            

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
                class="w-full md:w-auto ml-auto px-8 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors font-medium cursor-pointer relative min-h-[2.5rem] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-400 disabled:bg-zinc-400"
                @if($reclamo->estado->id == 4 || !$editable) disabled @endif>
                <!-- Estado normal -->
                <span wire:loading.remove wire:target="save" x-show="!showSuccess" class="flex items-center whitespace-nowrap">
                    Modificar Reclamo
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
                    ¡Reclamo Modificado!
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
                            <td class="px-6 py-4">
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
                                    @if($tipo->estado_id == 4 )
                                        @if(Auth::user()->rol->lReclamosFinaliza)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endif 
                                    @else 
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endif
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
                                <label class="inline-flex items-center ml-4" {{ $reclamo->categoria->privada ? 'hidden' : '' }}>
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

     <!-- Modal del Mapa -->
    @if($mostrarMapa)
        <!-- Contenedor principal del modal -->
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Overlay de fondo -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" wire:click="cerrarMapa"></div>

            <!-- Contenedor de centrado -->
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal content -->
                <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all">
                    
                    <!-- Header -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Seleccionar Ubicación Exacta
                            </h3>
                            <button 
                                wire:click="cerrarMapa"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contenido del mapa -->
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <!-- Contenedor del mapa -->
                        <div id="mapa-container" class="w-full h-96 bg-gray-200 dark:bg-gray-600 rounded-lg border border-gray-300 dark:border-gray-600 mb-4"></div>
                        
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-4">
                            <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                                <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Haga clic en el mapa para seleccionar la ubicación exacta del reclamo. El marcador se puede arrastrar para ajustar la posición.</span>
                            </p>
                        </div>

                        <!-- Información de ubicación seleccionada -->
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación seleccionada:</p>
                            <p id="direccion-seleccionada" class="text-sm text-gray-600 dark:text-gray-400">
                                Seleccione una ubicación en el mapa
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
                        <div class="flex items-center justify-end space-x-4">
                            
                            <!-- Botón Cancelar -->
                            <button 
                                wire:click="cerrarMapa"
                                type="button" 
                                class="cursor-pointer inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-500 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition-colors">
                                Cancelar
                            </button>

                            <!-- Botón Confirmar -->
                            <button 
                                id="btn-confirmar-ubicacion"
                                type="button" 
                                onclick="confirmarUbicacionSeleccionada()"
                                disabled
                                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-300 text-white cursor-not-allowed text-base font-medium sm:text-sm transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Confirmar Ubicación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif       
    
    @push('scripts')
  
    <script>
        // Variables globales para control de inicialización
        if (typeof mapa === 'undefined') {
            var mapa = null;
            var marcador;
            var geocoder;
            var autocompleteFormulario;
            var ubicacionSeleccionada = null;
            var mapaInicializado = false;
            var googleMapsLoaded = false;
            var googleMapsLoading = false;
            var livewireListenersInitialized = false;
            var ultimaPosicionConocida = { lat: -34.6549, lng: -59.4307 }; // Mercedes por defecto
        }
        

        function inicializarMapa() {
            const mapaContainer = document.getElementById('mapa-container');
            if (!mapaContainer) {
                console.error('Contenedor del mapa no encontrado');
                return;
            }

            // Verificar que Google Maps esté completamente cargado
            if (!window.google || !window.google.maps || !window.google.maps.Map) {
                console.error('Google Maps API no está completamente cargada para el mapa');
                return;
            }

            // SIEMPRE limpiar y recrear
            mapaContainer.innerHTML = '';
            
            if (marcador) {
                marcador.setMap(null);
                marcador = null;
            }
            mapa = null;
            ubicacionSeleccionada = null;

            try {
                // Usar objeto literal en lugar de constructor LatLng
                const centroInicial = ultimaPosicionConocida;
                
                mapa = new google.maps.Map(mapaContainer, {
                    zoom: 17,
                    center: centroInicial,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                geocoder = new google.maps.Geocoder();

                google.maps.event.addListenerOnce(mapa, 'idle', function() {
                    google.maps.event.trigger(mapa, 'resize');
                    mapa.setCenter(centroInicial);
                    
                    if (ultimaPosicionConocida.lat !== -34.6549 || ultimaPosicionConocida.lng !== -59.4307) {
                        // Usar objeto literal en lugar de constructor
                        agregarMarcador(ultimaPosicionConocida);
                    }
                });

                mapa.addListener('click', function(evento) {
                    ultimaPosicionConocida = {
                        lat: evento.latLng.lat(),
                        lng: evento.latLng.lng()
                    };
                    agregarMarcador(evento.latLng);
                    geocodificarPosicion(evento.latLng);
                });

                if (ultimaPosicionConocida.lat === -34.6549 && ultimaPosicionConocida.lng === -59.4307) {
                    obtenerUbicacionActual();
                }
                
            } catch (error) {
                console.error('Error al inicializar mapa:', error);
            }
        }

        // 3. AGREGAR esta función nueva para debugging
        function verificarEstadoMapa() {
            
            const container = document.getElementById('mapa-container');
            if (container) {
                console.log('Dimensiones del contenedor:', container.offsetWidth, 'x', container.offsetHeight);
                console.log('Contenedor visible:', container.offsetWidth > 0 && container.offsetHeight > 0);
            }
        }

       function inicializarAutocompleteFormulario() {
            const input = document.getElementById('direccion-autocomplete');
            if (!input) {
                console.log('Input de dirección no encontrado');
                return false;
            }

            // Verificar si ya está inicializado
            if (input.hasAttribute('data-autocomplete-ready')) {
                console.log('Autocomplete ya inicializado en este input');
                return true;
            }

            // CLAVE: Verificar que Google Maps esté completamente cargado
            if (!window.google || !window.google.maps || !window.google.maps.LatLng || !window.google.maps.places) {
                console.error('Google Maps API no está completamente cargada');
                return false;
            }

            try {
                // Limpiar autocomplete anterior si existe
                if (autocompleteFormulario) {
                    google.maps.event.clearInstanceListeners(autocompleteFormulario);
                }

                // Centro de Mercedes, Buenos Aires - USAR OBJETO LITERAL EN LUGAR DE LatLng CONSTRUCTOR
                const centroMercedes = { lat: -34.6549, lng: -59.4307 };
                
                // Calcular bounds usando objeto literal
                const radioEnGrados = 0.09; // Aproximadamente 10km
                const boundsRestrictivos = new google.maps.LatLngBounds(
                    { lat: centroMercedes.lat - radioEnGrados, lng: centroMercedes.lng - radioEnGrados },
                    { lat: centroMercedes.lat + radioEnGrados, lng: centroMercedes.lng + radioEnGrados }
                );

                autocompleteFormulario = new google.maps.places.Autocomplete(input, {
                    bounds: boundsRestrictivos,
                    strictBounds: true,
                    componentRestrictions: { country: 'ar' },
                    types: ['address'],
                    fields: ['geometry', 'formatted_address', 'address_components', 'name']
                });

                // Variable para rastrear si hay predicciones disponibles
                let tienePredictones = false;
                let service = new google.maps.places.AutocompleteService();

                // Función para calcular distancia entre dos puntos (fórmula de Haversine)
                function calcularDistancia(lat1, lng1, lat2, lng2) {
                    const R = 6371; // Radio de la Tierra en km
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLng = (lng2 - lng1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                            Math.sin(dLng/2) * Math.sin(dLng/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c; // Distancia en km
                }

                autocompleteFormulario.addListener('place_changed', function() {
                    const place = autocompleteFormulario.getPlace();
                    
                    if (!place.geometry || !place.geometry.location) {
                        console.log('No se encontró información de ubicación');
                        mostrarEstadoDireccion('error');
                        return;
                    }

                    const location = place.geometry.location;
                    const direccionCompleta = place.formatted_address;
                    
                    // Verificar distancia (filtro adicional de seguridad)
                    const distancia = calcularDistancia(
                        centroMercedes.lat, 
                        centroMercedes.lng,
                        location.lat(), 
                        location.lng()
                    );

                    if (distancia > 10) {
                        console.log(`Dirección fuera del rango permitido: ${distancia.toFixed(2)}km`);
                        mostrarEstadoDireccion('error');
                        alert('La dirección seleccionada está fuera del área de servicio (10km de radio)');
                        input.value = '';
                        return;
                    }

                    // Guardar esta posición como la última conocida para el mapa
                    ultimaPosicionConocida = {
                        lat: location.lat(),
                        lng: location.lng()
                    };
                    
                    // Actualizar Livewire
                    @this.set('direccion', direccionCompleta);
                    @this.set('coordenadas', location.lat() + ',' + location.lng());
                    @this.set('direccionCompleta', direccionCompleta);
                    @this.set('mostrarCalleSeleccionada',true);

                    mostrarEstadoDireccion('success');
                });

                // Manejar el evento keydown para detectar Enter
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        
                        if (tienePredictones && input.value.trim()) {
                            const request = {
                                input: input.value,
                                bounds: boundsRestrictivos,
                                componentRestrictions: { country: 'ar' },
                                types: ['address']
                            };

                            service.getPlacePredictions(request, (predictions, status) => {
                                if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length > 0) {
                                    const prediccionesFiltradas = [];
                                    let procesadas = 0;
                                    
                                    predictions.forEach((prediction) => {
                                        const placesService = new google.maps.places.PlacesService(document.createElement('div'));
                                        
                                        placesService.getDetails({
                                            placeId: prediction.place_id,
                                            fields: ['geometry', 'formatted_address']
                                        }, (place, status) => {
                                            procesadas++;
                                            
                                            if (status === google.maps.places.PlacesServiceStatus.OK && place && place.geometry) {
                                                const distancia = calcularDistancia(
                                                    centroMercedes.lat, 
                                                    centroMercedes.lng,
                                                    place.geometry.location.lat(), 
                                                    place.geometry.location.lng()
                                                );
                                                
                                                if (distancia <= 10) {
                                                    prediccionesFiltradas.push({
                                                        place: place,
                                                        distancia: distancia
                                                    });
                                                }
                                            }
                                            
                                            if (procesadas === predictions.length) {
                                                if (prediccionesFiltradas.length > 0) {
                                                    prediccionesFiltradas.sort((a, b) => a.distancia - b.distancia);
                                                    const mejorOpcion = prediccionesFiltradas[0].place;
                                                    
                                                    input.value = mejorOpcion.formatted_address;
                                                    
                                                    const location = mejorOpcion.geometry.location;
                                                    
                                                    ultimaPosicionConocida = {
                                                        lat: location.lat(),
                                                        lng: location.lng()
                                                    };
                                                    
                                                    @this.set('direccion', mejorOpcion.formatted_address);
                                                    @this.set('coordenadas', location.lat() + ',' + location.lng());
                                                    @this.set('direccionCompleta', mejorOpcion.formatted_address);
                                                    @this.set('mostrarCalleSeleccionada', true);

                                                    mostrarEstadoDireccion('success');
                                                } else {
                                                    alert('No se encontraron direcciones dentro del área de servicio (10km)');
                                                    mostrarEstadoDireccion('error');
                                                }
                                            }
                                        });
                                    });
                                }
                            });
                        }
                    }
                });

                // Manejar cambios manuales y detectar si hay predicciones
                let timeoutId;
                input.addEventListener('input', function(e) {
                    clearTimeout(timeoutId);
                    
                    if (!e.target.value.trim()) {
                        mostrarEstadoDireccion('reset');
                        tienePredictones = false;
                        return;
                    }
                    
                    timeoutId = setTimeout(() => {
                        const request = {
                            input: e.target.value,
                            bounds: boundsRestrictivos,
                            componentRestrictions: { country: 'ar' },
                            types: ['address']
                        };

                        service.getPlacePredictions(request, (predictions, status) => {
                            if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length > 0) {
                                tienePredictones = true;
                            } else {
                                tienePredictones = false;
                            }
                        });
                    }, 300);
                });

                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                    }
                });

                input.setAttribute('data-autocomplete-ready', 'true');
                return true;

            } catch (error) {
                console.error('Error al inicializar autocomplete:', error);
                return false;
            }
        }

        function sincronizarPosicionDesdeFormulario() {
            // Esta función se puede llamar cuando Livewire ya tiene coordenadas
            // para sincronizarlas con la posición del mapa
            
            // Obtener las coordenadas actuales desde Livewire
            const coordenadasActuales = @this.get('coordenadas');
            
            if (coordenadasActuales && coordenadasActuales.includes(',')) {
                const [lat, lng] = coordenadasActuales.split(',').map(coord => parseFloat(coord.trim()));
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    ultimaPosicionConocida = { lat, lng };
                    return true;
                }
            }
            
            return false;
        }

        function mostrarEstadoDireccion(estado) {
            const statusDiv = document.getElementById('direccion-status');
            const mensajeDiv = document.getElementById('direccion-mensaje');
            const successIcon = document.getElementById('direccion-success');
            const errorIcon = document.getElementById('direccion-error');
            const validadaMsg = document.getElementById('direccion-validada');
            const noValidadaMsg = document.getElementById('direccion-no-validada');

            // Resetear todos los estados
            [statusDiv, successIcon, errorIcon, validadaMsg, noValidadaMsg, mensajeDiv].forEach(el => {
                if (el) el.classList.add('hidden');
            });

            if (estado === 'success') {
                if (statusDiv) statusDiv.classList.remove('hidden');
                if (successIcon) successIcon.classList.remove('hidden');
                if (mensajeDiv) mensajeDiv.classList.remove('hidden');
                if (validadaMsg) validadaMsg.classList.remove('hidden');
            } else if (estado === 'error' || estado === 'warning') {
                if (statusDiv) statusDiv.classList.remove('hidden');
                if (errorIcon) errorIcon.classList.remove('hidden');
                if (mensajeDiv) mensajeDiv.classList.remove('hidden');
                if (noValidadaMsg) noValidadaMsg.classList.remove('hidden');
            }
        }

        function obtenerUbicacionActual() {
            if (navigator.geolocation) {
                const opciones = {
                    enableHighAccuracy: false,
                    timeout: 5000,
                    maximumAge: 60000 
                };
                
                navigator.geolocation.getCurrentPosition(
                    function(posicion) {
                        const { latitude, longitude } = posicion.coords;
                        const ubicacionActual = new google.maps.LatLng(latitude, longitude);
                        
                        // NUEVO: Guardar esta posición como la última conocida
                        ultimaPosicionConocida = {
                            lat: latitude,
                            lng: longitude
                        };
                                              
                        mapa.setCenter(ubicacionActual);
                        mapa.setZoom(17);
                        agregarMarcador(ubicacionActual);
                        geocodificarPosicion(ubicacionActual);
                    },
                    function(error) {
                        console.warn('Error al obtener ubicación:', error.message);
                        // Si falla, usar la última posición conocida o Mercedes
                    },
                    opciones
                );
            }
        }


        function agregarMarcador(posicion, direccion = null) {
            if (marcador) {
                marcador.setMap(null);
            }

            // Normalizar la posición (puede ser LatLng o objeto literal)
            let posicionNormalizada;
            if (posicion.lat && typeof posicion.lat === 'function') {
                // Es un objeto LatLng
                posicionNormalizada = posicion;
            } else if (posicion.lat && posicion.lng) {
                // Es un objeto literal
                posicionNormalizada = new google.maps.LatLng(posicion.lat, posicion.lng);
            } else {
                console.error('Posición inválida:', posicion);
                return;
            }

            marcador = new google.maps.Marker({
                position: posicionNormalizada,
                map: mapa,
                draggable: true,
                title: 'Ubicación del reclamo - Arrastra para ajustar',
                animation: google.maps.Animation.DROP
            });

            ubicacionSeleccionada = {
                lat: posicionNormalizada.lat(),
                lng: posicionNormalizada.lng(),
                direccion: direccion
            };

            const btnConfirmar = document.getElementById('btn-confirmar-ubicacion');
            if (btnConfirmar) {
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('bg-gray-300', 'cursor-not-allowed');
                btnConfirmar.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            }
            
            if (direccion) {
                mostrarDireccionSeleccionada(direccion);
            } else {
                geocodificarPosicion(posicionNormalizada);
            }

            marcador.addListener('dragend', function() {
                const nuevaPosicion = marcador.getPosition();
                ubicacionSeleccionada.lat = nuevaPosicion.lat();
                ubicacionSeleccionada.lng = nuevaPosicion.lng();
                geocodificarPosicion(nuevaPosicion);
            });
        }

        function geocodificarPosicion(posicion) {
            geocoder.geocode({ location: posicion }, function(resultados, estado) {
                if (estado === 'OK' && resultados[0]) {
                    const direccion = resultados[0].formatted_address;
                    ubicacionSeleccionada.direccion = direccion;
                    mostrarDireccionSeleccionada(direccion);
                } else {
                    const coordenadas = `Lat: ${posicion.lat().toFixed(6)}, Lng: ${posicion.lng().toFixed(6)}`;
                    ubicacionSeleccionada.direccion = coordenadas;
                    mostrarDireccionSeleccionada(coordenadas);
                }
            });
        }

        function mostrarDireccionSeleccionada(direccion) {
            const elemento = document.getElementById('direccion-seleccionada');
            if (elemento) {
                elemento.textContent = direccion;
            }
        }

        function limpiarFormularioBusqueda() {
            const btnConfirmar = document.getElementById('btn-confirmar-ubicacion');
            if (btnConfirmar) {
                btnConfirmar.disabled = true;
                btnConfirmar.classList.add('bg-gray-300', 'cursor-not-allowed');
                btnConfirmar.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            }
            
            const direccionSeleccionada = document.getElementById('direccion-seleccionada');
            if (direccionSeleccionada) {
                direccionSeleccionada.textContent = 'Seleccione una ubicación en el mapa';
            }
            
            ubicacionSeleccionada = null;
        }

        function confirmarUbicacionSeleccionada() {
            if (ubicacionSeleccionada) {
                // NUEVO: Guardar esta posición para la próxima vez
                ultimaPosicionConocida = {
                    lat: ubicacionSeleccionada.lat,
                    lng: ubicacionSeleccionada.lng
                };
                
                Livewire.dispatch('confirmar-ubicacion-mapa', {
                    lat: ubicacionSeleccionada.lat,
                    lng: ubicacionSeleccionada.lng,
                    direccion: ubicacionSeleccionada.direccion
                });
            } else {
                alert('Por favor, seleccione una ubicación en el mapa');
            }
        }

        function cargarGoogleMaps() {
            if (googleMapsLoaded) {
                return Promise.resolve();
            }

            if (googleMapsLoading) {
                return new Promise((resolve) => {
                    const checkLoaded = setInterval(() => {
                        if (googleMapsLoaded) {
                            clearInterval(checkLoaded);
                            resolve();
                        }
                    }, 100);
                });
            }

            // Verificación más robusta
            if (typeof google !== 'undefined' && 
                google.maps && 
                google.maps.places && 
                google.maps.LatLng && 
                google.maps.Map) {
                googleMapsLoaded = true;
                return Promise.resolve();
            }

            const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
            if (existingScript) {
                googleMapsLoading = true;
                return new Promise((resolve) => {
                    const checkGlobalGoogle = setInterval(() => {
                        if (typeof google !== 'undefined' && 
                            google.maps && 
                            google.maps.places && 
                            google.maps.LatLng && 
                            google.maps.Map) {
                            clearInterval(checkGlobalGoogle);
                            googleMapsLoaded = true;
                            googleMapsLoading = false;
                            resolve();
                        }
                    }, 100);
                });
            }

            googleMapsLoading = true;
            
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyArpDAi1ugbTSLT4wlr4T_qMmBZLouBfxo&libraries=places,marker&loading=async`;
                script.async = true;
                script.defer = true;
                
                script.onload = function() {
                    // Esperar un momento adicional para asegurar que todo esté cargado
                    setTimeout(() => {
                        if (typeof google !== 'undefined' && 
                            google.maps && 
                            google.maps.places && 
                            google.maps.LatLng && 
                            google.maps.Map) {
                            googleMapsLoaded = true;
                            googleMapsLoading = false;
                            resolve();
                        } else {
                            reject(new Error('Google Maps API no se cargó completamente'));
                        }
                    }, 500);
                };
                
                script.onerror = function() {
                    googleMapsLoading = false;
                    reject(new Error('Error al cargar Google Maps API'));
                };
                
                if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
                    document.head.appendChild(script);
                }
            });
        }
        // 2. Simplificar inicializarEventListeners - REEMPLAZAR COMPLETA
        function inicializarEventListeners() {
                if (livewireListenersInitialized) {
                    return;
                }

                livewireListenersInitialized = true;
                
                Livewire.on('inicializar-mapa', () => {
                    
                    // NUEVO: Intentar sincronizar posición desde el formulario antes de abrir el mapa
                    sincronizarPosicionDesdeFormulario();
                    
                    cargarGoogleMaps().then(() => {
                        // Dar tiempo para que el modal se muestre completamente
                        setTimeout(() => {
                            const container = document.getElementById('mapa-container');
                            if (container) {
                                inicializarMapa();
                            } else {
                                console.error('Contenedor no encontrado después del timeout');
                            }
                        }, 400);
                    }).catch(error => {
                        console.error('Error cargando Google Maps:', error);
                    });
                });

                Livewire.on('ubicacion-confirmada', (event) => {
                    if (marcador) {
                        marcador.setMap(null);
                        marcador = null;
                    }
                    ubicacionSeleccionada = null;
                    mostrarEstadoDireccion('success');
                });

                // Hook para autocomplete
                Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        const input = document.getElementById('direccion-autocomplete');
                        
                        if (input && googleMapsLoaded && !input.hasAttribute('data-autocomplete-ready')) {
                            inicializarAutocompleteFormulario();
                        }
                    }, 150);
                });
        }

        function initializeApp() {
            if (window.googleMapsAppInitialized) {
                return;
            }
            
            window.googleMapsAppInitialized = true;
            
            cargarGoogleMaps().then(() => {
                const input = document.getElementById('direccion-autocomplete');
                if (input) {
                    inicializarAutocompleteFormulario();
                }
            });
            
            inicializarEventListeners();
        }

        // Inicialización
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeApp);
        } else {
            initializeApp();
        }

        function resetearMapa() {
            // Limpiar estado anterior
            if (marcador) {
                marcador.setMap(null);
                marcador = null;
            }
            
            if (mapa) {
                mapa = null;
            }
            
            mapaInicializado = false;
            ubicacionSeleccionada = null;
            
            // Limpiar interfaz
            limpiarFormularioBusqueda();
        }

        
        document.addEventListener('livewire:init', () => {
            if (!window.googleMapsAppInitialized) {
                initializeApp();
            }
        });
        
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                const input = document.getElementById('direccion-autocomplete');
                if (input && googleMapsLoaded && !input.hasAttribute('data-autocomplete-ready')) {
                    inicializarAutocompleteFormulario();
                }
            }, 200);
        });

        // Función global
        window.confirmarUbicacionSeleccionada = confirmarUbicacionSeleccionada;
    </script>
@endpush
    <style>
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    /* Asegurar que el modal esté encima de todo */
.modal-mapa {
    z-index: 9999 !important;
}

.modal-mapa .modal-content {
    z-index: 10000 !important;
}
    </style>

</div>