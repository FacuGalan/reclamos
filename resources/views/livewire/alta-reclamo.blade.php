<div class="max-w-4xl mx-auto p-6">
    
    <!-- Notificación de éxito centrada (solo para área pública) -->
    @if(!$isPrivateArea)
        <div 
            x-data="{ show: false, reclamoData: {} }"
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-[-20px]"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 translate-y-[-20px]"
            @reclamo-creado-exitoso.window="
                reclamoData = $event.detail[0];
                show = true;
                setTimeout(() => { show = false }, 10000);
            "
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display: none;">
            
            <!-- Overlay semitransparente -->
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            
            <!-- Modal centrado -->
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Header verde con ícono -->
                <div class="bg-green-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-bold text-white">¡Reclamo Registrado Exitosamente!</h3>
                        </div>
                        <div class="ml-auto">
                            <button @click="show = false; window.location.href = '{{ route('home') }}'" class="text-white hover:text-gray-200 focus:outline-none">
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido -->
                <div class="px-6 py-6">
                    <div class="space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Número de reclamo:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">#<span x-text="reclamoData.id"></span></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Fecha:</span>
                            <span x-text="reclamoData.fecha"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Solicitante:</span>
                            <span x-text="reclamoData.nombre_completo"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Categoría:</span>
                            <span x-text="reclamoData.categoria"></span>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Guarde el número de reclamo para futuras consultas.
                        </p>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Redirigiendo al inicio en unos segundos...</p>
                        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1">
                            <div class="bg-green-500 h-1 rounded-full animate-pulse" style="width: 100%; animation: progress 10s linear;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($showSuccess)
        <!-- Pantalla de éxito -->
        <div class="text-center py-8">
            <div class="mb-6">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">¡Reclamo creado exitosamente!</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $successMessage }}</p>
                
                @if ($reclamoCreado)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            <strong>Número de reclamo:</strong> #{{ $reclamoCreado->id }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            <strong>Fecha:</strong> {{ $reclamoCreado->fecha }}
                        </p>
                    </div>
                @endif
            </div>
            
            <div class="space-x-4">
                <button 
                    wire:click="resetForm" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Crear otro reclamo
                </button>
                
                @if (!$redirectAfterSave)
                    <a href="{{ route('home') }}" 
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors inline-block">
                        Volver al inicio
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Formulario de creación -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                {{ $isPrivateArea ? 'Nuevo Reclamo' : 'Nuevo Reclamo' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Complete los datos del formulario para registrar {{ $isPrivateArea ? 'el' : 'su' }} reclamo</p>
        </div>

        <!-- Indicador de pasos -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                @if ($showPersonaForm)
                    <!-- Paso 1: Datos personales -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                    {{ $step >= 1 ? 'bg-[#77BF43] text-white' : 'bg-gray-200 text-gray-600' }}">
                            1
                        </div>
                        <span class="ml-2 text-sm {{ $step >= 1 ? 'text-[#77BF43] font-medium' : 'text-gray-500' }}">
                            Personales
                        </span>
                    </div>
                    
                    <!-- Línea conectora -->
                    <div class="w-12 h-0.5 {{ $step >= 2 ? 'bg-[#77BF43]' : 'bg-gray-200' }}"></div>
                @endif
                
                <!-- Paso 2: Datos del reclamo -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 2 ? 'bg-[#77BF43] text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $showPersonaForm ? '2' : '1' }}
                    </div>
                    <span class="ml-2 text-sm {{ $step >= 2 ? 'text-[#77BF43] font-medium' : 'text-gray-500' }}">
                        Reclamo
                    </span>
                </div>
                
                <!-- Línea conectora -->
                <div class="w-12 h-0.5 {{ $step >= 3 ? 'bg-[#77BF43]' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 3: Confirmación -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 3 ? 'bg-[#77BF43] text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $showPersonaForm ? '3' : '2' }}
                    </div>
                    <span class="ml-2 text-sm {{ $step >= 3 ? 'text-[#77BF43] font-medium' : 'text-gray-500' }}">
                        Confirmación
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido de los pasos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            @if ($step == 1 )
                <!-- Paso 1: Datos personales -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos Personales</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            DNI *
                        </label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="persona_dni"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                                {{ $personaEncontrada ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : '' }}"
                            placeholder="Ingrese su DNI">

                        @error('persona_dni') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                      
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre *
                        </label>
                        <input 
                            type="text" 
                            id="persona_nombre"
                            wire:model="persona_nombre"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                                {{ $personaEncontrada ? 'bg-gray-100 dark:bg-gray-600' : '' }}"
                            {{ $personaEncontrada ? 'readonly' : '' }}
                            placeholder="Ingrese su nombre">
                        @error('persona_nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Apellido *
                        </label>
                        <input 
                            type="text" 
                            id="persona_apellido"
                            wire:model="persona_apellido"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                                {{ $personaEncontrada ? 'bg-gray-100 dark:bg-gray-600' : '' }}"
                            {{ $personaEncontrada ? 'readonly' : '' }}
                            placeholder="Ingrese su apellido">
                        @error('persona_apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teléfono *
                        </label>
                        <input 
                            type="text" 
                            id="persona_telefono"
                            wire:model="persona_telefono"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese su teléfono">
                        @error('persona_telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="persona_email"
                            wire:model="persona_email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese su email">
                        @error('persona_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

            @elseif ($step == 2 )
                <!-- Paso 2: Datos del reclamo -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos del Reclamo</h2>
                
                <div class="space-y-6">
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div x-data="{
                                search: '',
                                open: false,
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
                                    this.selectedId = categoria.id;
                                    this.search = categoria.nombre;
                                    this.open = false;
                                },
                                
                                get selectedCategoria() {
                                    return this.categorias.find(c => c.id == this.selectedId);
                                }
                            }" 
                            class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Motivo *
                                </label>
                                
                                <!-- Input principal -->
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        x-model="search"
                                        @focus="open = true"
                                        @click="open = true"
                                        @blur="setTimeout(() => open = false, 150)"
                                        class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Busque o seleccione un motivo..."
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
                                        No se encontraron motivos que coincidan con su búsqueda
                                    </div>
                                </div>
                                
                                @error('categoria_id') 
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                                @enderror
                            </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dirección *
                        </label>
                        <input 
                            type="text" 
                            wire:model="direccion"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Entre qué calles se encuentra">
                        @error('entre_calles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción del reclamo *
                        </label>
                        <textarea 
                            wire:model="descripcion"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Describa detalladamente su reclamo"></textarea>
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Coordenadas / Ubicación *
                        </label>
                        <input 
                            type="text" 
                            wire:model="coordenadas"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Ej: -34.123456, -58.123456 o descripción específica">
                        @error('coordenadas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-sm text-gray-500 mt-1">Puede ingresar coordenadas GPS o una descripción específica de la ubicación</p>
                    </div>
                </div>

            @elseif ($step == 3 )
                <!-- Paso 3: Confirmación -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Confirmar Reclamo</h2>
                
                <div class="space-y-6">
                    @if ($showPersonaForm)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos Personales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <p><strong>DNI:</strong> {{ $persona_dni }}</p>
                                <p><strong>Nombre:</strong> {{ $persona_nombre }} {{ $persona_apellido }}</p>
                                <p><strong>Teléfono:</strong> {{ $persona_telefono }}</p>
                                <p><strong>Email:</strong> {{ $persona_email ?: 'No especificado' }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos del Reclamo</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Motivo del reclamo:</strong> 
                                @if($categoria_id)
                                    {{ $categorias->find($categoria_id)->nombre ?? 'Motivo no encontrado' }}
                                @endif
                            </p>
                            <p><strong>Dirección:</strong> {{ $direccion }}</p>
                            @if($entre_calles)
                                <p><strong>Entre calles:</strong> {{ $entre_calles }}</p>
                            @endif
                            <p><strong>Ubicación:</strong> {{ $coordenadas }}</p>
                            <p><strong>Descripción:</strong></p>
                            <p class="bg-white dark:bg-gray-600 p-3 rounded border">{{ $descripcion }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botones de navegación -->
            <div class="mt-8 flex justify-between">
                <div>
                    @if ($step > 1 && $showPersonaForm)
                        <button 
                            wire:click="previousStep"
                            class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                            Anterior
                        </button>
                    @elseif ($step > 1 && !$showPersonaForm)
                        <button 
                            wire:click="previousStep"
                            class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                            Anterior
                        </button>
                    @endif
                </div>
                
                <div>
                    @if (($step < 3 && $showPersonaForm) || ($step < 2 && !$showPersonaForm))
                        <button 
                            wire:click="nextStep"
                            class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                            Siguiente
                        </button>
                    @else
                        <button 
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer relative overflow-hidden">
                            <!-- Texto normal -->
                            <span wire:loading.remove>Crear Reclamo</span>
                            
                            <!-- Estado de guardando -->
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @if($isPrivateArea)
                                    Guardando...
                                @else
                                    Creando reclamo...
                                @endif
                            </span>

                            <!-- Animación de éxito (solo para área privada) -->
                            @if($isPrivateArea)
                                <span x-data="{ show: false }" 
                                      x-show="show"
                                      x-init="
                                        $wire.on('reclamo-creado-exitoso', () => {
                                            show = true; 
                                            setTimeout(() => show = false, 800)
                                        })
                                      "
                                      class="flex items-center"
                                      style="display: none;">
                                    <svg class="h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ¡Guardado!
                                </span>
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <style>
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    </style>

</div>