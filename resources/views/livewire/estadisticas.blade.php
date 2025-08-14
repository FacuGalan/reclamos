<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Estadísticas y Mapa de Calor</h1>
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

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $estadisticasRendimiento['porcentaje_finalizados'] }}%</div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-300">% Resolución</div>
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

    <div class="flex flex-col mt-0 items-center justify-between pt-0 dark:border-gray-600" >
        <button 
            wire:click="generarMapaCalor({{ true }})"
            wire:loading.attr="disabled"
            class="px-6 py-3 bg-[#77BF43] hover:bg-[#5a9032] text-white rounded-lg transition-colors cursor-pointer flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove>
                {{ $mostrarMapaCalor ? 'Actualizar' : 'Generar' }} Mapa de Calor
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

    <!-- Mapa de Calor -->
    @if($mostrarMapaCalor)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
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
                <!-- Contenedor del mapa -->
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
                <!-- Mensaje cuando no hay datos -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No hay datos para mostrar</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        No se encontraron reclamos con coordenadas válidas para los filtros seleccionados.
                        <br>Ajusta los filtros e intenta nuevamente.
                    </p>
                </div>
            @endif
        </div>
    @endif



    @push('scripts')
<script>
// Namespace para evitar conflictos con otros mapas
window.EstadisticasMapaCalor = window.EstadisticasMapaCalor || {};

(function(namespace) {
    // Variables privadas del namespace
    let mapa = null;
    let capaCalor = null;
    let puntos = [];
    let inicializado = false;
    
    // Función para verificar si Google Maps está disponible
    function isGoogleMapsLoaded() {
        return typeof google !== 'undefined' && 
               google.maps && 
               google.maps.Map && 
               google.maps.visualization && 
               google.maps.visualization.HeatmapLayer;
    }
    
    // Función para inicializar el mapa de calor
    namespace.inicializar = function(datosReclamos) {
        console.log('Inicializando mapa de calor con:', datosReclamos.length, 'reclamos');
        
        // Verificar que Google Maps esté disponible
        if (!isGoogleMapsLoaded()) {
            console.error('Google Maps no está completamente cargado');
            return;
        }
        
        const contenedor = document.getElementById('mapa-calor-container');
        if (!contenedor) {
            console.error('Contenedor del mapa de calor no encontrado');
            return;
        }

        // Limpiar mapa anterior de forma más robusta
        namespace.limpiar();
        contenedor.innerHTML = '';
        
        try {
            // Centro por defecto (Mercedes, Buenos Aires)
            const centro = { lat: -34.6549, lng: -59.4307 };
            
            // Crear el mapa con configuración específica
            mapa = new google.maps.Map(contenedor, {
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
            const puntosValidos = [];
            
            datosReclamos.forEach(reclamo => {
                if (reclamo.lat && reclamo.lng && 
                    !isNaN(reclamo.lat) && !isNaN(reclamo.lng) &&
                    reclamo.lat >= -90 && reclamo.lat <= 90 &&
                    reclamo.lng >= -180 && reclamo.lng <= 180) {
                    const punto = new google.maps.LatLng(reclamo.lat, reclamo.lng);
                    puntos.push(punto);
                    puntosValidos.push(reclamo);
                }
            });

            if (puntos.length === 0) {
                console.warn('No hay puntos válidos para mostrar en el mapa de calor');
                mostrarMensajeSinDatos(contenedor);
                return;
            }

            // Crear la capa de calor con configuración optimizada
            capaCalor = new google.maps.visualization.HeatmapLayer({
                data: puntos,
                map: mapa,
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

            // Ajustar vista si hay múltiples puntos
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

            // Ajustar radio según zoom
            mapa.addListener('zoom_changed', function() {
                if (capaCalor && !capaCalor.getMap()) {
                    return; // El mapa de calor fue removido
                }
                if (capaCalor) {
                    capaCalor.set('radius', getRadiusPorZoom(mapa.getZoom()));
                }
            });

            // Añadir marcadores informativos si hay pocos puntos
            if (puntosValidos.length <= 5) {
                agregarMarcadoresInformativos(puntosValidos);
            }

            // Forzar redibujado después de un momento
            setTimeout(() => {
                if (mapa) {
                    google.maps.event.trigger(mapa, 'resize');
                    mapa.setCenter(centro);
                }
            }, 100);

            console.log('Mapa de calor inicializado exitosamente con', puntos.length, 'puntos');
            inicializado = true;
            
        } catch (error) {
            console.error('Error al inicializar mapa de calor:', error);
            mostrarMensajeError(contenedor, error.message);
        }
    };

    // Función para mostrar mensaje cuando no hay datos
    function mostrarMensajeSinDatos(contenedor) {
        contenedor.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100 dark:bg-gray-700 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No hay puntos válidos para el mapa de calor</p>
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

    // Función para agregar marcadores informativos
    function agregarMarcadoresInformativos(reclamos) {
        if (!mapa || reclamos.length === 0) return;
        
        reclamos.forEach(reclamo => {
            const marcador = new google.maps.Marker({
                position: { lat: reclamo.lat, lng: reclamo.lng },
                map: mapa,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 6,
                    fillColor: '#FF4444',
                    fillOpacity: 0.9,
                    strokeWeight: 2,
                    strokeColor: '#FFFFFF'
                },
                title: `ID: ${reclamo.id} - ${reclamo.categoria}`,
                zIndex: 1000
            });

            // Info window con mejor contenido
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="p-3 min-w-[250px] max-w-[300px]">
                        <div class="font-semibold text-base mb-2 text-gray-800">#${reclamo.id} - ${reclamo.categoria}</div>
                        <div class="text-sm text-gray-600 mb-2 line-clamp-3">${reclamo.descripcion}</div>
                        <div class="text-xs text-gray-500 mb-1">📍 ${reclamo.direccion}</div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                            <span class="text-xs text-blue-600">${reclamo.fecha}</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 rounded">${reclamo.estado}</span>
                        </div>
                    </div>
                `,
                maxWidth: 350
            });

            marcador.addListener('click', function() {
                infoWindow.open(mapa, marcador);
            });
        });
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
                reject(new Error('Error al cargar Google Maps API para mapa de calor'));
                delete window.initGoogleMapsCallback;
            };
            
            document.head.appendChild(script);
        });
    };

    // Función para limpiar el mapa de forma segura
    namespace.limpiar = function() {
        try {
            if (capaCalor) {
                capaCalor.setMap(null);
                capaCalor = null;
            }
            if (mapa) {
                // Limpiar todos los listeners
                google.maps.event.clearInstanceListeners(mapa);
                mapa = null;
            }
            puntos = [];
            inicializado = false;
        } catch (error) {
            console.warn('Error al limpiar mapa de calor:', error);
            // Forzar limpieza
            mapa = null;
            capaCalor = null;
            puntos = [];
            inicializado = false;
        }
    };

    // Función para verificar si el mapa está activo
    namespace.estaActivo = function() {
        return inicializado && mapa && capaCalor;
    };

})(window.EstadisticasMapaCalor);

// Event listeners mejorados para Livewire
document.addEventListener('livewire:init', () => {
    console.log('Inicializando listeners de estadísticas');
    
    // Escuchar evento para inicializar mapa de calor
    Livewire.on('inicializar-mapa-calor', (event) => {
        console.log('Evento recibido para inicializar mapa de calor');
        
        const datosReclamos = event[0].reclamos || [];
        
        // Limpiar cualquier mapa anterior
        if (window.EstadisticasMapaCalor.estaActivo()) {
            window.EstadisticasMapaCalor.limpiar();
        }
        
        window.EstadisticasMapaCalor.cargarApi()
            .then(() => {
                // Dar tiempo para que el contenedor esté disponible
                setTimeout(() => {
                    window.EstadisticasMapaCalor.inicializar(datosReclamos);
                }, 500);
            })
            .catch(error => {
                console.error('Error cargando Google Maps para mapa de calor:', error);
            });
    });
});

// Limpiar cuando se navega fuera de la página
document.addEventListener('livewire:navigating', () => {
    console.log('Navegando fuera de estadísticas, limpiando mapa de calor');
    if (window.EstadisticasMapaCalor) {
        window.EstadisticasMapaCalor.limpiar();
    }
});

// Limpiar en navegación completa si no estamos en estadísticas
document.addEventListener('livewire:navigated', () => {
    if (!window.location.pathname.includes('estadisticas')) {
        console.log('Navegación completada fuera de estadísticas, limpiando mapa de calor');
        if (window.EstadisticasMapaCalor) {
            window.EstadisticasMapaCalor.limpiar();
        }
    }
});

// Limpiar al recargar la página
window.addEventListener('beforeunload', () => {
    if (window.EstadisticasMapaCalor) {
        window.EstadisticasMapaCalor.limpiar();
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