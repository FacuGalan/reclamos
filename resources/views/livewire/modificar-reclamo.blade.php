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
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">¡Reclamo actualizado exitosamente!</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Los cambios han sido guardados correctamente.</p>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <strong>Número de reclamo:</strong> #{{ $reclamo->id }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <strong>Fecha:</strong> {{ $reclamo->fecha }}
                    </p>
                </div>
            </div>
            
            <div class="space-x-4">
                <button 
                    wire:click="volverALista" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Volver a la lista
                </button>
            </div>
        </div>
    @else
        <!-- Formulario de modificación -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                Modificar Reclamo #{{ $reclamo->id ?? $reclamoId }}
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Modifique los datos del reclamo según sea necesario
            </p>
        </div>

        <!-- Indicador de pasos -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
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
                
                <!-- Paso 2: Datos del reclamo -->
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $step >= 2 ? 'bg-[#77BF43] text-white' : 'bg-gray-200 text-gray-600' }}">
                        2
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
                        3
                    </div>
                    <span class="ml-2 text-sm {{ $step >= 3 ? 'text-[#77BF43] font-medium' : 'text-gray-500' }}">
                        Confirmación
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenido de los pasos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            @if ($step == 1)
                <!-- Paso 1: Datos personales -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos de la Persona</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            DNI *
                        </label>
                        <input 
                            type="text" 
                            wire:model="persona_dni"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Ingrese el email">
                        @error('persona_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

            @elseif ($step == 2)
                <!-- Paso 2: Datos del reclamo -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Datos del Reclamo</h2>
                
                <div class="space-y-6">
                    <!-- Selector de categoría -->
                    <div class="grid grid-cols-1 gap-6">
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
                                        class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
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

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estado *
                        </label>
                        <select 
                            wire:model="estado_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Seleccione un estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                        @error('estado_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            placeholder="Describa detalladamente el reclamo"></textarea>
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

            @elseif ($step == 3)
                <!-- Paso 3: Confirmación -->
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Confirmar Cambios</h2>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos de la Persona</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <p><strong>DNI:</strong> {{ $persona_dni }}</p>
                            <p><strong>Nombre:</strong> {{ $persona_nombre }} {{ $persona_apellido }}</p>
                            <p><strong>Teléfono:</strong> {{ $persona_telefono }}</p>
                            <p><strong>Email:</strong> {{ $persona_email ?: 'No especificado' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-medium text-gray-800 dark:text-white mb-3">Datos del Reclamo</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Categoría:</strong> 
                                @if($categoria_id)
                                    {{ $categorias->find($categoria_id)->nombre ?? 'Categoría no encontrada' }}
                                @endif
                            </p>
                            <p><strong>Estado:</strong> 
                                @if($estado_id)
                                    {{ $estados->find($estado_id)->nombre ?? 'Estado no encontrado' }}
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
                    @if ($step > 1)
                        <button 
                            wire:click="previousStep"
                            class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                            Anterior
                        </button>
                    @else
                        <button 
                            wire:click="volverALista"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancelar
                        </button>
                    @endif
                </div>
                
                <div>
                    @if ($step < 3)
                        <button 
                            wire:click="nextStep"
                            class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer">
                            Siguiente
                        </button>
                    @else
                        <button 
                            wire:click="save"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                            Actualizar Reclamo
                        </button>
                    @endif
                </div>
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