<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Estadísticas y Mapas</h1>
            <p class="text-gray-600 dark:text-gray-300">
                Analiza la distribución geográfica y temporal de los reclamos
            </p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros de Análisis</h3>
            <div class="flex flex-col md:flex-row items-center gap-2 mt-4 md:mt-0">
                @if(!empty($resumenEstadisticas))
                    <button 
                        wire:click="exportarPDF"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors cursor-pointer flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar PDF
                    </button>
                @endif
                <button 
                    wire:click="limpiarFiltros"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors cursor-pointer">
                    Limpiar Filtros
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha desde</label>
                <input 
                    type="date" 
                    wire:model.live.debounce.300ms="filtro_fecha_desde"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha hasta</label>
                <input 
                    type="date" 
                    wire:model.live.debounce.300ms="filtro_fecha_hasta"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Filtro por área -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Área</label>
                <select 
                    wire:model.live="filtro_area"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro por categoría -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoría</label>
                <select 
                    wire:model.live="filtro_categoria"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            @if(!$ver_privada)
                <!-- Filtro por barrio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barrio</label>
                    <select 
                        wire:model.live="filtro_barrio"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos los barrios</option>
                        @foreach($barrios as $barrio)
                            <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cuadrilla</label>
                    <select 
                        wire:model.live="filtro_cuadrilla"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todas las cuadrillas</option>
                        @foreach($cuadrillas as $cuadrilla)
                            <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- Filtro por edificio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Edificio</label>
                    <select 
                        wire:model.live="filtro_edificio"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos los edificios</option>
                        @foreach($edificios as $edificio)
                            <option value="{{ $edificio->id }}">{{ $edificio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif  
        </div>
    </div>

    <!-- Estadísticas de rendimiento rápidas (siempre visibles) -->
    @if(!empty($estadisticasRendimiento))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Vista Rápida del Período</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $estadisticasRendimiento['total_reclamos'] }}</div>
                    <div class="text-xs text-blue-600 dark:text-blue-300">Total</div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $estadisticasRendimiento['finalizados'] }}</div>
                    <div class="text-xs text-green-600 dark:text-green-300">Finalizados</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-red-600 dark:text-red-400">{{ $estadisticasRendimiento['cancelados'] }}</div>
                    <div class="text-xs text-red-600 dark:text-red-300">Cancelados</div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $estadisticasRendimiento['activos'] }}</div>
                    <div class="text-xs text-yellow-600 dark:text-yellow-300">Activos</div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $estadisticasRendimiento['sin_asignar'] }}</div>
                    <div class="text-xs text-purple-600 dark:text-purple-300">Sin Asignar</div>
                </div>
                
                <!--div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $estadisticasRendimiento['asignados'] }}</div>
                    <div class="text-xs text-purple-600 dark:text-purple-300">Asignados</div>
                </div-->

                <!--div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-red-600 dark:text-red-400">{{ $estadisticasRendimiento['sin_asignar'] }}</div>
                    <div class="text-xs text-red-600 dark:text-red-300">Sin Asignar</div>
                </div-->

                <!--div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $estadisticasRendimiento['porcentaje_finalizados'] }}%</div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-300">% Resolución</div>
                </div-->

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ str_replace('.', ',', $estadisticasRendimiento['promedio_dias_resolucion']) }}</div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-300">Días resolución</div>
                </div>

                
            </div>

            <!-- Barra de progreso de resolución -->
            <div class="mt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progreso de Resolución</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $estadisticasRendimiento['porcentaje_finalizados'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $estadisticasRendimiento['porcentaje_finalizados'] }}%"></div>
                </div>
            </div>
        </div>
    @endif
    

    <!-- Mensajes de estado -->
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Resumen estadístico -->
    @if(count($resumenEstadisticas) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Resumen Estadístico</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalReclamos }}</div>
                    <div class="text-sm text-blue-600 dark:text-blue-300">Total de Reclamos</div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ count($resumenEstadisticas['por_categoria'] ?? []) }}
                    </div>
                    <div class="text-sm text-green-600 dark:text-green-300">Categorías Involucradas</div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ count($resumenEstadisticas['por_area'] ?? []) }}
                    </div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-300">Áreas Involucradas</div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ count($resumenEstadisticas['por_mes'] ?? []) }}
                    </div>
                    <div class="text-sm text-purple-600 dark:text-purple-300">Meses con Actividad</div>
                </div>
            </div>

            <!-- Gráficos estadísticos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Categorías -->
                @if(isset($resumenEstadisticas['por_categoria']) && count($resumenEstadisticas['por_categoria']) > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">Top Categorías</h4>
                        @foreach($resumenEstadisticas['por_categoria'] as $categoria => $cantidad)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $categoria }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $cantidad }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-3">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ ($cantidad / $totalReclamos) * 100 }}%"></div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Por Estado -->
                @if(isset($resumenEstadisticas['por_estado']) && count($resumenEstadisticas['por_estado']) > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3">Por Estado</h4>
                        @foreach($resumenEstadisticas['por_estado'] as $estado => $cantidad)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $estado }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $cantidad }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-3">
                                <div class="bg-green-600 h-2 rounded-full" 
                                     style="width: {{ ($cantidad / $totalReclamos) * 100 }}%"></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(!$ver_privada)
        <div class="flex flex-col mt-0 items-center justify-between pt-0 dark:border-gray-600" >
            <button 
                wire:click="generarMapaCalor({{ true }})"
                wire:loading.attr="disabled"
                class="px-6 py-3 bg-[#77BF43] hover:bg-[#5a9032] text-white rounded-lg transition-colors cursor-pointer flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove>
                    {{ $mostrarMapaCalor ? 'Actualizar' : 'Generar' }} Mapas
                </span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $mostrarMapaCalor ? 'Actualizando...' : 'Generando...' }}
                </span>
            </button>
            <!-- EL EXPORTAR POR AHORA LO SACO, HAY QUE VER QUE PUEDEN LLEGAR A QUERER EXPORTAR -->
            @if($mostrarMapaCalor && !empty($resumenEstadisticas) && false) 
                <button 
                    wire:click="exportarEstadisticas"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar Datos
                </button>
            @endif
        </div>
    @endif

    <!-- Mapas -->
    @if($mostrarMapaCalor)
        <!-- Mapa de Calor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Mapa de Calor de Reclamos
                    @if($totalReclamos > 0)
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            ({{ $totalReclamos }} reclamos encontrados)
                        </span>
                    @endif
                </h3>
                <button 
                    wire:click="cerrarMapa"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            @if($totalReclamos > 0)
                <!-- Contenedor del mapa de calor -->
                <div id="mapa-calor-container" class="w-full h-96 bg-gray-200 dark:bg-gray-600 rounded-lg border border-gray-300 dark:border-gray-600 mb-4"></div>
                
                <!-- Leyenda del mapa de calor -->
                <div class="flex items-center justify-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-r from-green-400 to-red-600"></div>
                        <span>Intensidad: Menor → Mayor concentración de reclamos</span>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center">
                        <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>El mapa de calor muestra la densidad de reclamos en el área. Las zonas más rojas indican mayor concentración de reclamos, mientras que las verdes indican menor concentración.</span>
                    </p>
                </div>
            @else
                <!-- Mensaje cuando no hay datos para mapa de calor -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay datos para el mapa de calor</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        No se encontraron reclamos con coordenadas válidas para los filtros seleccionados.
                    </p>
                </div>
            @endif
        </div>

        <!-- Mapa de Puntos Específicos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Ubicaciones Específicas de Reclamos
                    @if($totalReclamos > 0)
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            ({{ $totalReclamos }} ubicaciones)
                        </span>
                    @endif
                </h3>
            </div>

            @if($totalReclamos > 0)
                <!-- Contenedor del mapa de puntos -->
                <div id="mapa-puntos-container" class="w-full h-96 bg-gray-200 dark:bg-gray-600 rounded-lg border border-gray-300 dark:border-gray-600 mb-4"></div>
                
                <!-- Información del mapa de puntos -->
                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                        <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cada marcador representa un reclamo específico. Haz clic en los marcadores para ver los detalles del reclamo.</span>
                    </p>
                </div>
            @else
                <!-- Mensaje cuando no hay datos para mapa de puntos -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay ubicaciones para mostrar</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        No se encontraron reclamos con coordenadas válidas para los filtros seleccionados.
                    </p>
                </div>
            @endif
        </div>
    @endif



    @push('scripts')
<script>
// Namespace para evitar conflictos con otros mapas
// Namespace para evitar conflictos con otros mapas
window.EstadisticasMapas = window.EstadisticasMapas || {};

(function(namespace) {
    // Variables privadas del namespace
    let mapaCalor = null;
    let mapaPuntos = null;
    let capaCalor = null;
    let marcadores = [];
    let puntos = [];
    let inicializadoCalor = false;
    let inicializadoPuntos = false;
    
    // Función para verificar si Google Maps está disponible
    function isGoogleMapsLoaded() {
        return typeof google !== 'undefined' && 
               google.maps && 
               google.maps.Map && 
               google.maps.visualization && 
               google.maps.visualization.HeatmapLayer;
    }
    
    // Función para inicializar ambos mapas
    namespace.inicializar = function(datosReclamos) {
        console.log('Inicializando mapas con:', datosReclamos.length, 'reclamos');
        
        // Verificar que Google Maps esté disponible
        if (!isGoogleMapsLoaded()) {
            console.error('Google Maps no está completamente cargado');
            return;
        }
        
        // Limpiar mapas anteriores
        namespace.limpiar();
        
        // Inicializar mapa de calor
        namespace.inicializarMapaCalor(datosReclamos);
        
        // Inicializar mapa de puntos
        namespace.inicializarMapaPuntos(datosReclamos);
    };

    // Función para inicializar el mapa de calor
    namespace.inicializarMapaCalor = function(datosReclamos) {
        const contenedor = document.getElementById('mapa-calor-container');
        if (!contenedor) {
            console.error('Contenedor del mapa de calor no encontrado');
            return;
        }

        contenedor.innerHTML = '';
        
        try {
            // Centro por defecto (Mercedes, Buenos Aires)
            const centro = { lat: -34.6549, lng: -59.4307 };
            
            // Crear el mapa de calor
            mapaCalor = new google.maps.Map(contenedor, {
                zoom: 13,
                center: centro,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: "poi.business",
                        stylers: [{ visibility: "off" }]
                    },
                    {
                        featureType: "poi.medical",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });

            // Procesar puntos para el mapa de calor
            puntos = [];
            datosReclamos.forEach(reclamo => {
                if (reclamo.lat && reclamo.lng && 
                    !isNaN(reclamo.lat) && !isNaN(reclamo.lng) &&
                    reclamo.lat >= -90 && reclamo.lat <= 90 &&
                    reclamo.lng >= -180 && reclamo.lng <= 180) {
                    const punto = new google.maps.LatLng(reclamo.lat, reclamo.lng);
                    puntos.push(punto);
                }
            });

            if (puntos.length === 0) {
                console.warn('No hay puntos válidos para mostrar en el mapa de calor');
                mostrarMensajeSinDatos(contenedor, 'calor');
                return;
            }

            // Crear la capa de calor
            capaCalor = new google.maps.visualization.HeatmapLayer({
                data: puntos,
                map: mapaCalor,
                radius: getRadiusPorZoom(13),
                opacity: 0.8,
                maxIntensity: Math.max(3, Math.floor(puntos.length / 8)),
                gradient: [
                    'rgba(0, 255, 255, 0)',      // Transparente
                    'rgba(0, 255, 255, 0.2)',
                    'rgba(0, 191, 255, 0.4)',
                    'rgba(0, 127, 255, 0.6)',
                    'rgba(0, 63, 255, 0.8)',
                    'rgba(0, 0, 255, 1)',        // Azul
                    'rgba(63, 0, 191, 1)',
                    'rgba(127, 0, 127, 1)',
                    'rgba(191, 0, 63, 1)',
                    'rgba(255, 0, 0, 1)'         // Rojo
                ]
            });

            // Ajustar vista
            ajustarVistaMapa(mapaCalor, puntos);

            // Ajustar radio según zoom
            mapaCalor.addListener('zoom_changed', function() {
                if (capaCalor && capaCalor.getMap()) {
                    capaCalor.set('radius', getRadiusPorZoom(mapaCalor.getZoom()));
                }
            });

            console.log('Mapa de calor inicializado exitosamente');
            inicializadoCalor = true;
            
        } catch (error) {
            console.error('Error al inicializar mapa de calor:', error);
            mostrarMensajeError(contenedor, error.message);
        }
    };

    // Función para inicializar el mapa de puntos
    namespace.inicializarMapaPuntos = function(datosReclamos) {
        const contenedor = document.getElementById('mapa-puntos-container');
        if (!contenedor) {
            console.error('Contenedor del mapa de puntos no encontrado');
            return;
        }

        contenedor.innerHTML = '';
        
        try {
            // Centro por defecto (Mercedes, Buenos Aires)
            const centro = { lat: -34.6549, lng: -59.4307 };
            
            // Crear el mapa de puntos
            mapaPuntos = new google.maps.Map(contenedor, {
                zoom: 13,
                center: centro,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: "poi.business",
                        stylers: [{ visibility: "off" }]
                    },
                    {
                        featureType: "poi.medical",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });

            // Procesar reclamos válidos para marcadores
            const reclamosValidos = datosReclamos.filter(reclamo => 
                reclamo.lat && reclamo.lng && 
                !isNaN(reclamo.lat) && !isNaN(reclamo.lng) &&
                reclamo.lat >= -90 && reclamo.lat <= 90 &&
                reclamo.lng >= -180 && reclamo.lng <= 180
            );

            if (reclamosValidos.length === 0) {
                console.warn('No hay puntos válidos para mostrar en el mapa de puntos');
                mostrarMensajeSinDatos(contenedor, 'puntos');
                return;
            }

            // Crear marcadores para todos los reclamos
            marcadores = [];
            const puntosParaBounds = [];

            reclamosValidos.forEach((reclamo, index) => {
                const position = { lat: reclamo.lat, lng: reclamo.lng };
                puntosParaBounds.push(new google.maps.LatLng(reclamo.lat, reclamo.lng));
                
                // Definir colores por estado
                const colorPorEstado = {
                    'Finalizado': '#10B981',
                    'Cancelado': '#EF4444',
                    'En Proceso': '#F59E0B',
                    'Pendiente': '#6B7280',
                    'Asignado': '#3B82F6'
                };
                
                const color = colorPorEstado[reclamo.estado] || '#6B7280';
                
                const marcador = new google.maps.Marker({
                    position: position,
                    map: mapaPuntos,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: color,
                        fillOpacity: 0.9,
                        strokeWeight: 2,
                        strokeColor: '#FFFFFF'
                    },
                    title: `#${reclamo.id} - ${reclamo.categoria}`,
                    zIndex: 1000 + index
                });

                // Info window simplificada y funcional
                const descripcionCorta = reclamo.descripcion.length > 100 ? 
                    reclamo.descripcion.substring(0, 100) + '...' : 
                    reclamo.descripcion;

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="padding: 8px; max-width: 400px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <strong style="font-size: 16px;">#${reclamo.id}</strong>
                                <span style="background: ${color}; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px;">${reclamo.estado}</span>
                            </div>
                            <div style="margin-bottom: 6px;">
                                <strong>Categoría:</strong><br>
                                <span style="font-size: 13px;">${reclamo.categoria}</span>
                            </div>
                            <div style="margin-bottom: 6px;">
                                <strong>Descripción:</strong><br>
                                <span style="font-size: 12px;">${descripcionCorta}</span>
                            </div>
                            <div style="margin-bottom: 6px;">
                                <strong>Dirección:</strong><br>
                                <span style="font-size: 12px;">📍 ${reclamo.direccion}</span>
                            </div>
                            <div style="border-top: 1px solid #ccc; padding-top: 6px; margin-top: 8px; display: flex; justify-content: space-between; font-size: 11px;">
                                <span>📅 ${reclamo.fecha}</span>
                                <span>${reclamo.area}</span>
                            </div>
                        </div>
                    `,
                    maxWidth: 400
                });

                marcador.addListener('click', function() {
                    infoWindow.open(mapaPuntos, marcador);
                });

                marcadores.push(marcador);
            });

            // Ajustar vista
            ajustarVistaMapa(mapaPuntos, puntosParaBounds);

            console.log('Mapa de puntos inicializado exitosamente con', marcadores.length, 'marcadores');
            inicializadoPuntos = true;
            
        } catch (error) {
            console.error('Error al inicializar mapa de puntos:', error);
            mostrarMensajeError(contenedor, error.message);
        }
    };

    // Función para ajustar la vista del mapa
    function ajustarVistaMapa(mapa, puntos) {
        if (puntos.length > 1) {
            const bounds = new google.maps.LatLngBounds();
            puntos.forEach(punto => bounds.extend(punto));
            
            // Agregar padding al bounds
            const extendedBounds = new google.maps.LatLngBounds();
            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();
            const latPadding = (ne.lat() - sw.lat()) * 0.1;
            const lngPadding = (ne.lng() - sw.lng()) * 0.1;
            
            extendedBounds.extend(new google.maps.LatLng(ne.lat() + latPadding, ne.lng() + lngPadding));
            extendedBounds.extend(new google.maps.LatLng(sw.lat() - latPadding, sw.lng() - lngPadding));
            
            mapa.fitBounds(extendedBounds);
            
            // Limitar zoom
            google.maps.event.addListenerOnce(mapa, 'bounds_changed', function() {
                const currentZoom = mapa.getZoom();
                if (currentZoom > 16) {
                    mapa.setZoom(16);
                } else if (currentZoom < 11) {
                    mapa.setZoom(11);
                }
            });
        }
    }

    // Función para mostrar mensaje cuando no hay datos
    function mostrarMensajeSinDatos(contenedor, tipo) {
        const mensaje = tipo === 'calor' ? 
            'No hay puntos válidos para el mapa de calor' : 
            'No hay ubicaciones para mostrar';
            
        contenedor.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100 dark:bg-gray-700 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">${mensaje}</p>
                </div>
            </div>
        `;
    }

    // Función para mostrar mensaje de error
    function mostrarMensajeError(contenedor, mensaje) {
        contenedor.innerHTML = `
            <div class="flex items-center justify-center h-full bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-600 dark:text-red-400">Error al cargar el mapa</p>
                    <p class="text-sm text-red-500 dark:text-red-300 mt-1">${mensaje}</p>
                </div>
            </div>
        `;
    }

    // Función para calcular el radio basado en el zoom
    function getRadiusPorZoom(zoom) {
        if (zoom <= 11) return 35;
        if (zoom <= 13) return 28;
        if (zoom <= 15) return 22;
        if (zoom <= 16) return 18;
        return 15;
    }

    // Función mejorada para cargar la API de Google Maps
    namespace.cargarApi = function() {
        return new Promise((resolve, reject) => {
            // Si ya está cargada completamente
            if (isGoogleMapsLoaded()) {
                resolve();
                return;
            }

            // Verificar si hay scripts existentes
            const existingScripts = document.querySelectorAll('script[src*="maps.googleapis.com"]');
            
            if (existingScripts.length > 0) {
                // Ya hay un script cargándose, esperar a que termine
                const maxWait = 10000; // 10 segundos máximo
                const startTime = Date.now();
                
                const checkInterval = setInterval(() => {
                    if (isGoogleMapsLoaded()) {
                        clearInterval(checkInterval);
                        resolve();
                    } else if (Date.now() - startTime > maxWait) {
                        clearInterval(checkInterval);
                        reject(new Error('Timeout esperando a que Google Maps se cargue'));
                    }
                }, 200);
                return;
            }

            // Crear nuevo script solo si no existe ninguno
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyArpDAi1ugbTSLT4wlr4T_qMmBZLouBfxo&libraries=visualization,places&loading=async&callback=initGoogleMapsCallback`;
            script.async = true;
            script.defer = true;
            
            // Callback global para cuando se carga la API
            window.initGoogleMapsCallback = function() {
                console.log('Google Maps API cargada exitosamente');
                resolve();
                // Limpiar el callback
                delete window.initGoogleMapsCallback;
            };
            
            script.onerror = function(error) {
                console.error('Error al cargar Google Maps API:', error);
                reject(new Error('Error al cargar Google Maps API'));
                delete window.initGoogleMapsCallback;
            };
            
            document.head.appendChild(script);
        });
    };

    // Función para limpiar los mapas de forma segura
    namespace.limpiar = function() {
        try {
            // Limpiar mapa de calor
            if (capaCalor) {
                capaCalor.setMap(null);
                capaCalor = null;
            }
            if (mapaCalor) {
                google.maps.event.clearInstanceListeners(mapaCalor);
                mapaCalor = null;
            }
            
            // Limpiar mapa de puntos
            if (marcadores && marcadores.length > 0) {
                marcadores.forEach(marcador => marcador.setMap(null));
                marcadores = [];
            }
            if (mapaPuntos) {
                google.maps.event.clearInstanceListeners(mapaPuntos);
                mapaPuntos = null;
            }
            
            puntos = [];
            inicializadoCalor = false;
            inicializadoPuntos = false;
            
        } catch (error) {
            console.warn('Error al limpiar mapas:', error);
            // Forzar limpieza
            mapaCalor = null;
            mapaPuntos = null;
            capaCalor = null;
            marcadores = [];
            puntos = [];
            inicializadoCalor = false;
            inicializadoPuntos = false;
        }
    };

    // Función para verificar si los mapas están activos
    namespace.estanActivos = function() {
        return inicializadoCalor || inicializadoPuntos;
    };

})(window.EstadisticasMapas);

// Event listeners para Livewire
document.addEventListener('livewire:init', () => {
    console.log('Inicializando listeners de mapas de estadísticas');
    
    // Escuchar evento para inicializar mapas
    Livewire.on('inicializar-mapa-calor', (event) => {
        console.log('Evento recibido para inicializar mapas');
        
        const datosReclamos = event[0].reclamos || [];
        
        // Limpiar mapas anteriores
        if (window.EstadisticasMapas.estanActivos()) {
            window.EstadisticasMapas.limpiar();
        }
        
        window.EstadisticasMapas.cargarApi()
            .then(() => {
                // Dar tiempo para que los contenedores estén disponibles
                setTimeout(() => {
                    window.EstadisticasMapas.inicializar(datosReclamos);
                }, 500);
            })
            .catch(error => {
                console.error('Error cargando Google Maps:', error);
            });
    });
});

// Limpiar cuando se navega fuera de la página
document.addEventListener('livewire:navigating', () => {
    console.log('Navegando fuera de estadísticas, limpiando mapas');
    if (window.EstadisticasMapas) {
        window.EstadisticasMapas.limpiar();
    }
});

// Limpiar en navegación completa si no estamos en estadísticas
document.addEventListener('livewire:navigated', () => {
    if (!window.location.pathname.includes('estadisticas')) {
        console.log('Navegación completada fuera de estadísticas, limpiando mapas');
        if (window.EstadisticasMapas) {
            window.EstadisticasMapas.limpiar();
        }
    }
});

// Limpiar al recargar la página
window.addEventListener('beforeunload', () => {
    if (window.EstadisticasMapas) {
        window.EstadisticasMapas.limpiar();
    }
});
</script>
@endpush

    <style>
    /* Estilos específicos para el mapa de calor */
    #mapa-calor-container {
        position: relative;
        overflow: hidden;
    }

    /* Asegurar que el mapa sea responsive */
    #mapa-calor-container img {
        max-width: none !important;
    }

    /* Estilos para las info windows */
    .gm-style .gm-style-iw-c {
        padding: 0;
    }

    .gm-style .gm-style-iw-d {
        overflow: hidden !important;
    }

    /* Mejorar la apariencia de los controles del mapa */
    .gm-style .gmnoprint {
        background-color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Modo oscuro para las estadísticas */
    @media (prefers-color-scheme: dark) {
        .gm-style .gmnoprint {
            background-color: rgba(55, 65, 81, 0.9) !important;
        }
    }
    </style>
</div>