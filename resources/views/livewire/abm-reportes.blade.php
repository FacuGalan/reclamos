<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Reportes</h1>
            <p class="text-gray-600 dark:text-gray-300">
                Administra los reportes del sistema
            </p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros</h3>
            <button 
                wire:click="limpiarFiltros"
                class="px-4 py-2 bg-[#314158] hover:bg-[#4A5D76] text-white rounded-lg transition-colors cursor-pointer">
                Limpiar Filtros
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            <!-- Búsqueda general -->
            <div >
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Búsqueda</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Buscar por descripción, dirección, DNI o nombre...">
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

            <!-- Fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha desde</label>
                <input 
                    type="date" 
                    wire:model.live="filtro_fecha_desde"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha hasta</label>
                <input 
                    type="date" 
                    wire:model.live="filtro_fecha_hasta"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Tabla de reportes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Flash messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Vista Desktop: Tabla completa -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID / Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Solicitante
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dirección
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Detalle
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($reportes as $reporte)
                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $reporte->id }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reporte->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $reporte->persona ? $reporte->persona->nombre : 'Anónimo' }}
                                    @if($reporte->persona && $reporte->persona->dni)
                                        <br>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $reporte->persona->dni }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $reporte->categoria->nombre }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ Str::before($reporte->domicilio->direccion, ',') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reporte->domicilio->entre_calles ? 'Entre calles: ' . $reporte->domicilio->entre_calles : 'Sin entrecalles' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white" title="{{ $reporte->observaciones }}">
                                    {{ strlen($reporte->observaciones) > 150 ? substr($reporte->observaciones, 0, 150) . '...' : $reporte->observaciones }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button 
                                    wire:click="abrirModal({{ $reporte->id }})"
                                    class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full transition-colors duration-200"
                                    title="Ver detalle del reporte">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron reportes</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear un nuevo reporte.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vista Móvil: Tarjetas -->
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($reportes as $reporte)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <!-- Header de la tarjeta -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    #{{ $reporte->id }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $reporte->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $reporte->categoria->nombre }}
                            </h3>
                        </div>
                        
                        <!-- Botón de ver detalle -->
                        <button 
                            wire:click="abrirModal({{ $reporte->id }})"
                            class="ml-3 inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full transition-colors"
                            title="Ver detalle">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Información adicional colapsable -->
                    <div class="space-y-2 text-sm">
                        @if($reporte->persona)
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="truncate">{{ $reporte->persona->nombre }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-start text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate">{{ Str::limit(Str::before($reporte->domicilio->direccion, ','), 40) }}</span>
                        </div>
                        
                        @if($reporte->observaciones)
                            <div class="flex items-start text-gray-600 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <span class="line-clamp-2">{{ $reporte->observaciones }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium">No se encontraron reportes</p>
                        <p class="text-sm">Intenta ajustar los filtros</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($reportes->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $reportes->links() }}
            </div>
        @endif
    </div>

    @if($mostrarModal && $reporteSeleccionado)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" wire:click="cerrarModal"></div>

                <!-- Contenedor de centrado -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <!-- Modal content -->
                    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all max-h-[90vh] overflow-y-auto">
                        
                        <!-- Header -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 rounded-t-lg sticky top-0 z-10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Detalles del Reporte #{{ $reporteSeleccionado->id }}
                                </h3>
                                <button 
                                    wire:click="cerrarModal"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="px-6 py-6">
                            <div class="grid lg:grid-cols-2 gap-6">
                                <!-- Columna Izquierda: Datos Personales -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                        <svg class="w-5 h-5 inline-block mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Datos Personales
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        @if($reporteSeleccionado->persona)
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">DNI</label>
                                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->persona->dni ?? 'No especificado' }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</label>
                                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->persona->nombre ?? 'Anónimo' }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Apellido</label>
                                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->persona->apellido ?? 'Anónimo' }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</label>
                                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->persona->telefono ?? 'No especificado' }}</p>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->persona->email ?? 'No especificado' }}</p>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">Reporte anónimo</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Columna Derecha: Datos del Reporte -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                        <svg class="w-5 h-5 inline-block mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Datos del Reporte
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Categoría</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->categoria->nombre ?? 'N/A' }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha del Incidente</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $reporteSeleccionado->fecha ? \Carbon\Carbon::parse($reporteSeleccionado->fecha)->format('d/m/Y') : 'No especificada' }}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">¿Es habitual?</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $reporteSeleccionado->habitual ? 'Sí' : 'No' }}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Registro</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $reporteSeleccionado->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Ubicación -->
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                    <svg class="w-5 h-5 inline-block mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Ubicación
                                </h4>
                                
                                @if($reporteSeleccionado->domicilio)
                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->domicilio->direccion ?? 'No especificada' }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Entre Calles</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->domicilio->entre_calles ?? 'No especificado' }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($reporteSeleccionado->domicilio->direccion_rural)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Aclaraciones</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->domicilio->direccion_rural }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($reporteSeleccionado->domicilio->coordenadas)
                                        <!-- Mapa de solo lectura -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Ubicación en el Mapa</label>
                                            <div id="mapa-reporte-{{ $reporteSeleccionado->id }}" 
                                                class="w-full h-64 bg-gray-200 dark:bg-gray-600 rounded-lg border border-gray-300 dark:border-gray-600"
                                                data-coordenadas="{{ $reporteSeleccionado->domicilio->coordenadas }}">
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No hay información de ubicación disponible</p>
                                @endif
                            </div>

                            <!-- Descripción -->
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                    Descripción del Reporte
                                </h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $reporteSeleccionado->observaciones ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
                            <div class="flex justify-end">
                                <button 
                                    wire:click="cerrarModal"
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors cursor-pointer">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @push('scripts')
<script>
    // Variables globales
    let mapaReporteVista = null;
    let googleMapsLoadedReportes = false;

    // Función para cargar Google Maps
    function cargarGoogleMapsReportes() {
        if (googleMapsLoadedReportes) {
            return Promise.resolve();
        }

        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            googleMapsLoadedReportes = true;
            return Promise.resolve();
        }

        const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
        if (existingScript) {
            return new Promise((resolve) => {
                const checkLoaded = setInterval(() => {
                    if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                        clearInterval(checkLoaded);
                        googleMapsLoadedReportes = true;
                        resolve();
                    }
                }, 100);
            });
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyArpDAi1ugbTSLT4wlr4T_qMmBZLouBfxo&libraries=places&loading=async`;
            script.async = true;
            script.defer = true;
            
            script.onload = () => {
                setTimeout(() => {
                    if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                        googleMapsLoadedReportes = true;
                        resolve();
                    } else {
                        reject(new Error('Google Maps no se cargó completamente'));
                    }
                }, 300);
            };
            
            script.onerror = () => reject(new Error('Error al cargar Google Maps API'));
            
            document.head.appendChild(script);
        });
    }

    // Función para inicializar el mapa
    function inicializarMapaReporte(reporteId, coordenadas) {
        const contenedor = document.getElementById(`mapa-reporte-${reporteId}`);
        
        if (!contenedor) {
            console.error('Contenedor del mapa no encontrado:', `mapa-reporte-${reporteId}`);
            return;
        }

        if (!coordenadas || !coordenadas.includes(',')) {
            contenedor.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400"><p class="text-center">No hay coordenadas disponibles</p></div>';
            return;
        }

        const [lat, lng] = coordenadas.split(',').map(coord => parseFloat(coord.trim()));
        
        if (isNaN(lat) || isNaN(lng)) {
            contenedor.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400"><p class="text-center">Coordenadas no válidas</p></div>';
            return;
        }

        try {
            const posicion = { lat, lng };
            
            // Limpiar y preparar contenedor
            contenedor.innerHTML = '';
            contenedor.style.width = '100%';
            contenedor.style.height = '400px';
            contenedor.style.position = 'relative';
            
            // Crear mapa
            mapaReporteVista = new google.maps.Map(contenedor, {
                zoom: 16,
                center: posicion,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                gestureHandling: 'cooperative'
            });

            // Agregar marcador
            new google.maps.Marker({
                position: posicion,
                map: mapaReporteVista,
                title: 'Ubicación del reporte',
                animation: google.maps.Animation.DROP
            });

            // Asegurar renderizado correcto
            google.maps.event.addListenerOnce(mapaReporteVista, 'idle', () => {
                google.maps.event.trigger(mapaReporteVista, 'resize');
                mapaReporteVista.setCenter(posicion);
            });

        } catch (error) {
            console.error('Error al inicializar mapa:', error);
            contenedor.innerHTML = '<div class="flex items-center justify-center h-full text-red-500 dark:text-red-400"><p class="text-center">Error al cargar el mapa</p></div>';
        }
    }

    // Escuchar el evento de Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('inicializar-mapa-reporte', (event) => {
            //console.log('🎯 Evento recibido:', event);
            
            // Extraer datos - Livewire 3 pasa los parámetros como propiedades del objeto
            const reporteId = event.reporteId || event.detail?.reporteId;
            const coordenadas = event.coordenadas || event.detail?.coordenadas;
            
            //console.log('📍 ReporteId:', reporteId, 'Coordenadas:', coordenadas);
            
            if (!reporteId || !coordenadas) {
                console.error('❌ Faltan datos del evento');
                return;
            }
            
            // Esperar a que el modal esté completamente renderizado
            setTimeout(() => {
                cargarGoogleMapsReportes()
                    .then(() => {
                        setTimeout(() => {
                            inicializarMapaReporte(reporteId, coordenadas);
                        }, 300);
                    })
                    .catch(error => {
                        console.error('Error cargando Google Maps:', error);
                    });
            }, 100);
        });
    });

    // Limpiar cuando se cierra el modal
    document.addEventListener('click', (e) => {
        if (e.target.closest('[wire\\:click="cerrarModal"]')) {
            if (mapaReporteVista) {
                mapaReporteVista = null;
            }
        }
    });
</script>
@endpush
</div>
