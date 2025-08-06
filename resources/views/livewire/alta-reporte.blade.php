<div class="max-w-4xl mx-auto p-1">
    <style>
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    <!-- Notificación de éxito centrada (solo para área pública) -->
    <div 
        x-data="{ show: false, reporteData: {} }"
        x-show="show"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform scale-95 translate-y-[-20px]"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 translate-y-[-20px]"
        @reporte-creado-exitoso.window="
            reporteData = $event.detail[0];
            show = true;
            setTimeout(() => { show = false }, 10000);
        "
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        
        <!-- Overlay semitransparente -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal centrado -->
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4 border border-black dark:border-gray-700 overflow-hidden">
            
            <!-- Header verde con ícono -->
            <div class="bg-green-500 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-bold text-white">¡Reporte Registrado Exitosamente!</h3>
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
                        <span class="font-medium">Número de reporte:</span>
                        <span class="font-bold text-green-600 dark:text-green-400">#<span x-text="reporteData.id"></span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Fecha:</span>
                        <span x-text="reporteData.fecha"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Solicitante:</span>
                        <span x-text="reporteData.nombre_completo"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Categoría:</span>
                        <span x-text="reporteData.categoria"></span>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Guarde el número de reporte para futuras consultas.
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
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">¡Reporte enviado exitosamente!</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $successMessage }}</p>
                
                @if ($reporteCreado)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            <strong>Número de reporte:</strong> #{{ $reporteCreado->id }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            <strong>Fecha:</strong> {{ $reporteCreado->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif
            </div>
            
            <div class="space-x-4">
                <button 
                    wire:click="resetForm" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Enviar otro reporte
                </button>
                
                <a href="{{ route('home') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors inline-block">
                    Volver al inicio
                </a>
            </div>
        </div>
    @else
        <!-- Formulario de creación -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                Nuevo Reporte de Seguridad
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Complete el formulario para enviar un reporte anónimo de seguridad</p>
        </div>

        <!-- Indicador de pasos -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                <!-- Paso 1: Datos personales (opcional) -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 1 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        1
                    </div>
                    <span class="hidden sm:block sm:ml-2 text-sm {{ $step >= 1 ? 'text-blue-500 font-medium' : 'text-gray-500' }}">
                        Datos (Opcional)
                    </span>
                </div>
                
                <!-- Línea conectora -->
                <div class="w-12 h-0.5 {{ $step >= 2 ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 2: Datos del reporte -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 2 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        2
                    </div>
                    <span class="hidden sm:block sm:ml-2 text-sm {{ $step >= 2 ? 'text-blue-500 font-medium' : 'text-gray-500' }}">
                        Reporte
                    </span>
                </div>
                
                <!-- Línea conectora -->
                <div class="w-12 h-0.5 {{ $step >= 3 ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 3: Confirmación -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 3 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        3
                    </div>
                    <span class="hidden sm:block sm:ml-2 text-sm {{ $step >= 3 ? 'text-blue-500 font-medium' : 'text-gray-500' }}">
                        Confirmación
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido de los pasos -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            @if ($step == 1)
                <!-- Paso 1: Datos personales opcionales -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Datos Personales (Opcional)</h2>
                
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Puede enviar el reporte de forma completamente anónima o proporcionar sus datos para seguimiento.
                    </p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="incluir_datos_personales" class="mr-2">
                        <span class="text-gray-700 dark:text-gray-300">Quiero incluir mis datos personales</span>
                    </label>
                </div>
                
                @if($incluir_datos_personales)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                DNI
                            </label>
                            <input 
                                type="number" 
                                wire:model.live.debounce.300ms="dni"
                                class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Su DNI">
                            @error('dni') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nombre
                            </label>
                            <input 
                                type="text" 
                                wire:model="nombre"
                                class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Su nombre">
                            @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Apellido
                            </label>
                            <input 
                                type="text" 
                                wire:model="apellido"
                                class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Su nombre">
                            @error('apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Teléfono
                            </label>
                            <input 
                                type="text" 
                                wire:model="telefono"
                                class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Su teléfono">
                            @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email
                            </label>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Su email">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

            @elseif ($step == 2)
                <!-- Paso 2: Datos del reporte -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos del Reporte</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría *
                        </label>
                        <select 
                            wire:model="categoria_id"
                            class="w-full px-3 py-2 border border-gray-300 bg-white dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Seleccione una categoría</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

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
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Entre calles
                        </label>
                        <input 
                            type="text" 
                            wire:model="entre_calles"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-white"
                            placeholder="Entre qué calles se encuentra">
                        @error('entre_calles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                 @if($coordenadas)
                       <div class="md:col-span-2">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Ubicación confirmada: {{ $direccionCompleta ?: $direccion }} 
                                </p>
                            </div>
                       </div>
                    @endif

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción del reporte *
                        </label>
                        <textarea 
                        wire:model="descripcion"
                        rows="4"
                        class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Describa detalladamente lo que quiere reportar"></textarea>
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha del incidente
                            </label>
                            <input 
                            type="date"
                            wire:model="fecha_incidente"
                            class="w-full px-3 py-2 bg-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('fecha_incidente') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 mt-4">
                                ¿Es un hecho que se repite?
                            </label>
                                <input 
                                    type="checkbox" 
                                    wire:model="habitual"
                                    class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                    Sí, es habitual
                            @error('habitual') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            @elseif ($step == 3)
                <!-- Paso 3: Confirmación -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Confirmar Reporte</h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($incluir_datos_personales)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos Personales</h3>
                                <div class="text-sm space-y-1">
                                    <p><strong>Nombre:</strong> {{ $nombre ?: 'No especificado' }}</p>
                                    <p><strong>Teléfono:</strong> {{ $telefono ?: 'No especificado' }}</p>
                                    <p><strong>Email:</strong> {{ $email ?: 'No especificado' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Este reporte será enviado de forma anónima
                                </p>
                            </div>
                        @endif
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos del Reporte</h3>
                            <div class="text-sm space-y-1">
                                <p><strong>Categoría:</strong> {{ collect($categorias)->firstWhere('id', $categoria_id)?->nombre }}</p>
                                <p><strong>Dirección:</strong> {{ $direccion }}</p>
                                <p><strong>Fecha del incidente:</strong> {{ $fecha_incidente ?: 'No especificada' }}</p>
                                <p><strong>Descripción:</strong> {{ $descripcion }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botones de navegación -->
            <div class="mt-8 flex justify-between">
                <div>
                    @if ($step > 1)
                        <button 
                            wire:click="previousStep"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            Anterior
                        </button>
                    @endif
                </div>
                
                <div>
                    @if ($step < 3)
                        <button 
                            wire:click="nextStep"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            Siguiente
                        </button>
                    @else
                        <button 
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                            <span wire:loading.remove>Enviar Reporte</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enviando...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
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
            let mapa;
            let marcador;
            let geocoder;
            let autocompleteFormulario;
            let ubicacionSeleccionada = null;
            let mapaInicializado = false;
            let googleMapsLoaded = false;
            let googleMapsLoading = false;
            let livewireListenersInitialized = false;
            let ultimaPosicionConocida = { lat: -34.6549, lng: -59.4307 }; // Mercedes por defecto


            function inicializarMapa() {
                
                const mapaContainer = document.getElementById('mapa-container');
                if (!mapaContainer) {
                    console.error('Contenedor del mapa no encontrado');
                    return;
                }

                // SIEMPRE limpiar y recrear, sin verificar si ya está inicializado
                mapaContainer.innerHTML = '';
                
                // Resetear variables globales
                if (marcador) {
                    marcador.setMap(null);
                    marcador = null;
                }
                mapa = null;
                ubicacionSeleccionada = null;

                try {
                    // CAMBIO: Usar la última posición conocida en lugar de siempre Mercedes
                    const centroInicial = ultimaPosicionConocida;
                    
                    mapa = new google.maps.Map(mapaContainer, {
                        zoom: 17, // Aumentar zoom para mejor precisión
                        center: centroInicial,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                    geocoder = new google.maps.Geocoder();

                    // Forzar que el mapa se redibuje correctamente
                    google.maps.event.addListenerOnce(mapa, 'idle', function() {

                        google.maps.event.trigger(mapa, 'resize');
                        mapa.setCenter(centroInicial);
                        
                        // NUEVO: Si hay coordenadas previas, crear marcador
                        if (ultimaPosicionConocida.lat !== -34.6549 || ultimaPosicionConocida.lng !== -59.4307) {
                            agregarMarcador(new google.maps.LatLng(ultimaPosicionConocida.lat, ultimaPosicionConocida.lng));
                        }
                    });

                    // Listeners del mapa
                    mapa.addListener('click', function(evento) {

                        // NUEVO: Guardar la nueva posición
                        ultimaPosicionConocida = {
                            lat: evento.latLng.lat(),
                            lng: evento.latLng.lng()
                        };
                        agregarMarcador(evento.latLng);
                        geocodificarPosicion(evento.latLng);
                    });

                    // Intentar obtener ubicación actual solo si es la primera vez
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

                try {
                    // Limpiar autocomplete anterior si existe
                    if (autocompleteFormulario) {
                        google.maps.event.clearInstanceListeners(autocompleteFormulario);
                    }

                    autocompleteFormulario = new google.maps.places.Autocomplete(input, {
                        bounds: new google.maps.LatLngBounds(
                            new google.maps.LatLng(-34.7549, -59.5307),
                            new google.maps.LatLng(-34.5549, -59.3307)
                        ),
                        strictBounds: false,
                        componentRestrictions: { country: 'ar' },
                        types: ['address'],
                        fields: ['geometry', 'formatted_address', 'address_components', 'name']
                    });

                    autocompleteFormulario.addListener('place_changed', function() {
                        const place = autocompleteFormulario.getPlace();
                        
                        if (!place.geometry || !place.geometry.location) {
                            console.log('No se encontró información de ubicación');
                            mostrarEstadoDireccion('error');
                            return;
                        }

                        const location = place.geometry.location;
                        const direccionCompleta = place.formatted_address;
                        
                        // NUEVO: Guardar esta posición como la última conocida para el mapa
                        ultimaPosicionConocida = {
                            lat: location.lat(),
                            lng: location.lng()
                        };
                        
                        // Actualizar Livewire
                        @this.set('direccion', direccionCompleta);
                        @this.set('coordenadas', location.lat() + ',' + location.lng());
                        @this.set('direccionCompleta', direccionCompleta);

                        mostrarEstadoDireccion('success');

                    });

                    // Manejar cambios manuales
                    let timeoutId;
                    input.addEventListener('input', function(e) {
                        clearTimeout(timeoutId);
                        
                        if (!e.target.value.trim()) {
                            mostrarEstadoDireccion('reset');
                            return;
                        }
                        
                        timeoutId = setTimeout(() => {
                            if (!autocompleteFormulario.getPlace() || !autocompleteFormulario.getPlace().geometry) {
                                //mostrarEstadoDireccion('warning');
                            }
                        }, 1000);
                    });

                    // Marcar como inicializado
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

                marcador = new google.maps.Marker({
                    position: posicion,
                    map: mapa,
                    draggable: true,
                    title: 'Ubicación del reclamo - Arrastra para ajustar',
                    animation: google.maps.Animation.DROP
                });

                ubicacionSeleccionada = {
                    lat: posicion.lat(),
                    lng: posicion.lng(),
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
                    geocodificarPosicion(posicion);
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

                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    googleMapsLoaded = true;
                    return Promise.resolve();
                }

                const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
                if (existingScript) {
                    googleMapsLoading = true;
                    return new Promise((resolve) => {
                        const checkGlobalGoogle = setInterval(() => {
                            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
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
                        googleMapsLoaded = true;
                        googleMapsLoading = false;
                        resolve();
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
            /* Asegurar que el modal esté encima de todo */
            .modal-mapa {
                z-index: 9999 !important;
            }

            .modal-mapa .modal-content {
                z-index: 10000 !important;
            }
        </style>

    </div>
</div>