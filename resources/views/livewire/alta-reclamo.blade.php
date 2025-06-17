<div class="max-w-4xl mx-auto p-6">
    
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
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
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Nuevo Reclamo</h1>
            <p class="text-gray-600 dark:text-gray-300">Complete los datos del formulario para registrar su reclamo</p>
        </div>

        <!-- Indicador de pasos -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                @if ($showPersonaForm)
                    <!-- Paso 1: Datos personales -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                    {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                            1
                        </div>
                        <span class="ml-2 text-sm {{ $step >= 1 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                            Personales
                        </span>
                    </div>
                    
                    <!-- Línea conectora -->
                    <div class="w-12 h-0.5 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                @endif
                
                <!-- Paso 2: Datos del reclamo -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $showPersonaForm ? '2' : '1' }}
                    </div>
                    <span class="ml-2 text-sm {{ $step >= 2 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                        Reclamo
                    </span>
                </div>
                
                <!-- Línea conectora -->
                <div class="w-12 h-0.5 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 3: Confirmación -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $showPersonaForm ? '3' : '2' }}
                    </div>
                    <span class="ml-2 text-sm {{ $step >= 3 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                        Confirmación
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido de los pasos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            @if ($step == 1 && $showPersonaForm)
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
                            wire:model="persona_apellido"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                                {{ $personaEncontrada ? 'bg-gray-100 dark:bg-gray-600' : '' }}"
                            {{ $personaEncontrada ? 'readonly' : '' }}
                            placeholder="Ingrese su apellido">
                        @error('persona_apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teléfono
                        </label>
                        <input 
                            type="text" 
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
                            wire:model="persona_email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese su email">
                        @error('persona_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

            @elseif ($step == 2 || ($step == 1 && !$showPersonaForm))
                <!-- Paso 2: Datos del reclamo -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos del Reclamo</h2>
                
                <div class="space-y-6">
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo *
                            </label>
                            <select 
                                wire:model="categoria_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                {{ empty($categorias) ? 'disabled' : '' }}>
                                <option value="">Seleccione un motivo</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

            @elseif ($step == 3 || ($step == 2 && !$showPersonaForm))
                <!-- Paso 3: Confirmación -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Confirmar Reclamo</h2>
                
                <div class="space-y-6">
                    @if ($showPersonaForm)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos Personales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <p><strong>DNI:</strong> {{ $persona_dni }}</p>
                                <p><strong>Nombre:</strong> {{ $persona_nombre }} {{ $persona_apellido }}</p>
                                <p><strong>Teléfono:</strong> {{ $persona_telefono ?: 'No especificado' }}</p>
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
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Anterior
                    </button>
                @elseif ($step > 1 && !$showPersonaForm)
                    <button 
                        wire:click="previousStep"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Anterior
                    </button>
                @endif
            </div>
            
            <div>
                @if (($step < 3 && $showPersonaForm) || ($step < 2 && !$showPersonaForm))
                    <button 
                        wire:click="nextStep"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Siguiente
                    </button>
                @else
                    <button 
                        wire:click="save"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Crear Reclamo
                    </button>
                @endif
            </div>
        </div>
        </div>

        
    @endif

</div>