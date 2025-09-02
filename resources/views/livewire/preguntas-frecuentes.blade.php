<div class="max-w-6xl mx-auto bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 min-h-screen">
    <!-- Header institucional -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8 border-l-4 border-blue-400">
        <div class="flex items-center mb-4">
            <div class="bg-blue-400 rounded-full p-3 mr-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
            </div>  
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Preguntas Frecuentes</h2>
                <p class="text-gray-600 mt-1">Municipalidad de Mercedes - Atención al Ciudadano</p>
            </div>
        </div>
        <p class="text-gray-700">Accede a información sobre trámites, horarios de atención y contactos útiles</p>
    </div>

    <!-- Campo de búsqueda institucional -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-200 mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Buscar información
            </label>

            <div class="relative">
                <!-- Input -->
                <input 
                    type="text" 
                    wire:model.live.debounce.200ms="search" 
                    placeholder="Buscar por pregunta, respuesta o área de atención..."
                    class="w-full border text-left text-black border-gray-300 rounded-md py-3 pr-10 pl-4 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />

                <!-- Botón limpiar -->
                @if($search)
                    <button 
                        wire:click="$set('search', '')" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        title="Limpiar búsqueda"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Resultados de búsqueda -->
            @if($search)
                <p class="mt-2 text-sm text-gray-600">
                    Resultados para: <span class="font-semibold text-blue-600">"{{ $search }}"</span>
                </p>
            @endif
        </div>
    </div>

    <!-- Estadísticas institucionales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-50 rounded-lg p-3 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $preguntas->count() }}</p>
                    <p class="text-sm text-gray-600">Consultas Disponibles</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                                        <div class="bg-blue-50 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $preguntas->count() }}</p>
                    <p class="text-sm text-gray-600">Resultados Mostrados</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="bg-orange-50 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $preguntas->pluck('area.nombre')->filter()->unique()->count() ?: '0' }}</p>
                    <p class="text-sm text-gray-600">Áreas de Atención</p>
                </div>
            </div>
        </div>
    </div>
    

    
<!-- Cards de preguntas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @forelse($preguntas as $index => $pregunta)
            @if($mostrarTodas || $index < 4)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-gray-200">
                    <!-- Header de la pregunta -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight flex-1 pr-4">
                                {{ $pregunta->pregunta }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 flex-shrink-0">
                                {{ $pregunta->area->nombre ?? 'General' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Contenido de la respuesta -->
                    <div class="p-6">
                        <div class="text-gray-700 leading-relaxed">
                            {{ $pregunta->respuesta }}
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <!-- Estado vacío -->
            <div class="col-span-1 lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-12 text-center border border-gray-200 max-w-xl mx-auto">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">No se encontraron resultados</h3>
                    @if($search)
                        <p class="text-gray-600 mb-6">
                            No encontramos información que coincida con <span class="font-semibold text-blue-600">"{{ $search }}"</span>
                        </p>
                        <button 
                            wire:click="$set('search', '')" 
                            class="inline-flex items-center px-4 py-2 mb-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors"
                        >
                            Ver todas las consultas
                        </button>
                    @else
                        <p class="text-gray-600">No hay consultas frecuentes disponibles en este momento.</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Botón Ver más / Ver menos -->
    @if(count($preguntas) > 4)
        <div class="text-center mb-8">
            @if(!$mostrarTodas)
                <button 
                    wire:click="$set('mostrarTodas', true)" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-400 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Ver más preguntas ({{ count($preguntas) - 4 }})
                </button>
            @else
                <button 
                    wire:click="$set('mostrarTodas', false)" 
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-white bg-blue-400 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Ver menos
                </button>
            @endif
        </div>
    @endif

    @if($preguntas->count() > 0)
        <!-- Información de contacto institucional -->
        <div class="mt-12 bg-white rounded-lg shadow-md p-8 border-l-4 border-green-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">¿Necesitas más información?</h3>
                    <p class="text-gray-600 mb-4">Si no encontraste la respuesta que buscabas, podes contactarte directamente con nosotros.</p>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                            <span class="text-sm"><strong>147</strong></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col space-y-3">
                    <a href="/nuevo-reclamo" class="inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Realizar Reclamo
                    </a>
                    <a href="/nuevo-reporte" class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        Realizar Reporte
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

