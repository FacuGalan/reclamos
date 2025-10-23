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
        <!-- Header responsive -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros</h3>
            
            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-2">
                <button 
                    wire:click="exportarExcel"
                    class="px-4 py-2 bg-[#217346] hover:bg-[#2e8b5c] text-white rounded-lg transition-colors cursor-pointer text-sm">
                    Exportar Excel
                </button>
                <button 
                    wire:click="limpiarFiltros"
                    class="px-4 py-2 bg-[#314158] hover:bg-[#4A5D76] text-white rounded-lg transition-colors cursor-pointer">
                    Limpiar Filtros
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            <!-- Búsqueda general -->
            <div>
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

            <!-- Filtro por estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                <select 
                    wire:model.live="filtro_estado"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}">
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Toggle Mostrar finalizados -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mostrar finalizados</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="mostrar_finalizados" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $mostrar_finalizados ? 'Sí' : 'No' }}
                    </span>
                </label>
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
                            Estado
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reporte->estado)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reporte->estado->finalizacion ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                        {{ $reporte->estado->nombre }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Sin estado
                                    </span>
                                @endif
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
                                <div class="flex justify-center gap-2">
                                    <button 
                                        wire:click="abrirModal({{ $reporte->id }})"
                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full transition-colors duration-200"
                                        title="Ver detalle del reporte">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="abrirModalCambioEstado({{ $reporte->id }})"
                                        class="inline-flex items-center justify-center p-2 text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-full transition-colors duration-200"
                                        title="Cambiar estado">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron reportes</p>
                                    <p class="text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vista Mobile: Cards -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($reportes as $reporte)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <!-- Header del card -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                #{{ $reporte->id }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                {{ $reporte->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                        @if($reporte->estado)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reporte->estado->finalizacion ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                {{ $reporte->estado->nombre }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Sin estado
                            </span>
                        @endif
                    </div>

                    <!-- Contenido -->
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Solicitante:</span>
                            <span class="text-gray-900 dark:text-white">
                                {{ $reporte->persona ? $reporte->persona->nombre : 'Anónimo' }}
                                @if($reporte->persona && $reporte->persona->dni)
                                    ({{ $reporte->persona->dni }})
                                @endif
                            </span>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Categoría:</span>
                            <span class="text-gray-900 dark:text-white">{{ $reporte->categoria->nombre }}</span>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Dirección:</span>
                            <span class="text-gray-900 dark:text-white">
                                {{ Str::before($reporte->domicilio->direccion, ',') }}
                            </span>
                        </div>
                        
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Descripción:</span>
                            <p class="text-gray-900 dark:text-white mt-1">
                                {{ strlen($reporte->observaciones) > 100 ? substr($reporte->observaciones, 0, 100) . '...' : $reporte->observaciones }}
                            </p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-2 mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <button 
                            wire:click="abrirModal({{ $reporte->id }})"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm font-medium">Ver</span>
                        </button>
                        <button 
                            wire:click="abrirModalCambioEstado({{ $reporte->id }})"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span class="text-sm font-medium">Estado</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron reportes</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $reportes->links() }}
        </div>
    </div>

    <!-- Modal de visualización -->
    @if($mostrarModal && $reporteSeleccionado)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay con menos opacidad -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="cerrarModal"></div>
            
            <!-- Contenedor del modal -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Modal -->
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full z-50">
                    <!-- Header del Modal -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">
                                Detalle del Reporte #{{ $reporteSeleccionado->id }}
                            </h3>
                            <button 
                                wire:click="cerrarModal"
                                class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contenido del Modal -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información General -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Información General
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Fecha del incidente:</span>
                                            <span class="text-gray-900 dark:text-white block">
                                                {{ \Carbon\Carbon::parse($reporteSeleccionado->fecha)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Categoría:</span>
                                            <span class="text-gray-900 dark:text-white block">
                                                {{ $reporteSeleccionado->categoria->nombre ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                                            <div class="mt-1">
                                                @if($reporteSeleccionado->estado)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reporteSeleccionado->estado->finalizacion ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                                        {{ $reporteSeleccionado->estado->nombre }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        Sin estado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Habitual:</span>
                                            <span class="text-gray-900 dark:text-white block">
                                                {{ $reporteSeleccionado->habitual ? 'Sí' : 'No' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Creado el:</span>
                                            <span class="text-gray-900 dark:text-white block">
                                                {{ $reporteSeleccionado->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Denunciante -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Denunciante
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        @if($reporteSeleccionado->persona)
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Nombre:</span>
                                                <span class="text-gray-900 dark:text-white block">
                                                    {{ $reporteSeleccionado->persona->nombre }} {{ $reporteSeleccionado->persona->apellido }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">DNI:</span>
                                                <span class="text-gray-900 dark:text-white block">
                                                    {{ $reporteSeleccionado->persona->dni ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Teléfono:</span>
                                                <span class="text-gray-900 dark:text-white block">
                                                    {{ $reporteSeleccionado->persona->telefono ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Email:</span>
                                                <span class="text-gray-900 dark:text-white block">
                                                    {{ $reporteSeleccionado->persona->email ?? 'N/A' }}
                                                </span>
                                            </div>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400">Denuncia anónima</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Ubicación
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        @if($reporteSeleccionado->domicilio)
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Dirección:</span>
                                                <span class="text-gray-900 dark:text-white block">
                                                    {{ $reporteSeleccionado->domicilio->direccion ?? 'N/A' }}
                                                </span>
                                            </div>
                                            @if($reporteSeleccionado->domicilio->entre_calles)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Entre calles:</span>
                                                    <span class="text-gray-900 dark:text-white block">
                                                        {{ $reporteSeleccionado->domicilio->entre_calles }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if($reporteSeleccionado->domicilio->direccion_rural)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Aclaraciones:</span>
                                                    <span class="text-gray-900 dark:text-white block">
                                                        {{ $reporteSeleccionado->domicilio->direccion_rural }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if($reporteSeleccionado->domicilio->barrio)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Barrio:</span>
                                                    <span class="text-gray-900 dark:text-white block">
                                                        {{ $reporteSeleccionado->domicilio->barrio->nombre }}
                                                    </span>
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400">Sin información de domicilio</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                        Descripción
                                    </h4>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $reporteSeleccionado->observaciones ?? 'Sin descripción' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Mapa -->
                            @if($reporteSeleccionado->domicilio && $reporteSeleccionado->domicilio->coordenadas)
                                <div class="md:col-span-2">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            Ubicación en el Mapa
                                        </h4>
                                        <div id="mapa-reporte-{{ $reporteSeleccionado->id }}" class="w-full rounded-lg bg-gray-200 dark:bg-gray-600" style="height: 400px;"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end">
                        <button 
                            wire:click="cerrarModal"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Cambio de Estado -->
    @if($mostrarModalEstado)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay con menos opacidad -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="cerrarModalEstado"></div>
            
            <!-- Contenedor del modal -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Modal -->
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
                    <!-- Header del Modal -->
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 dark:from-yellow-600 dark:to-yellow-700 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Cambiar Estado - Reporte #{{ $reporteParaCambioEstado->id }}
                            </h3>
                            <button 
                                wire:click="cerrarModalEstado"
                                class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contenido del Modal -->
                    <div class="px-6 py-4">
                        <!-- Estado Actual -->
                        @if($reporteParaCambioEstado->estado)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">Estado actual:</span>
                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $reporteParaCambioEstado->estado->finalizacion ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                    {{ $reporteParaCambioEstado->estado->nombre }}
                                </span>
                            </div>
                        @else
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado actual:</span>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Sin estado asignado</span>
                            </div>
                        @endif

                        <!-- Select de Nuevo Estado -->
                        <div class="mb-4">
                            <label for="nuevoEstado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nuevo Estado <span class="text-red-500">*</span>
                            </label>
                            <select 
                                wire:model="nuevoEstadoId" 
                                id="nuevoEstado"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('nuevoEstadoId') border-red-500 @enderror">
                                <option value="">Seleccione un estado</option>
                                @foreach(App\Models\ReporteEstado::orderBy('id')->get() as $estado)
                                    <option value="{{ $estado->id }}">
                                        {{ $estado->nombre }}
                                        @if($estado->finalizacion)
                                            (Finalizar)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('nuevoEstadoId')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-4">
                            <label for="observacionesCambio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones (opcional)
                            </label>
                            <textarea 
                                wire:model="observacionesCambioEstado" 
                                id="observacionesCambio"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Agregue comentarios sobre el cambio de estado..."></textarea>
                        </div>

                        <!-- Historial de Cambios -->
                        @if($reporteParaCambioEstado->estadoCambios->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Historial de Cambios
                                </h4>
                                <div class="max-h-60 overflow-y-auto space-y-3">
                                    @foreach($reporteParaCambioEstado->estadoCambios()->latest()->get() as $cambio)
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border-l-4 border-blue-500">
                                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 mb-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $cambio->created_at->format('d/m/Y H:i') }}
                                                @if($cambio->usuario)
                                                    <span class="mx-1">•</span>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $cambio->usuario->name }}
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($cambio->estadoAnterior)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                                        {{ $cambio->estadoAnterior->nombre }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                                        Sin estado
                                                    </span>
                                                @endif
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                    {{ $cambio->estadoNuevo->nombre }}
                                                </span>
                                            </div>
                                            @if($cambio->observaciones)
                                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                    </svg>
                                                    <span>{{ $cambio->observaciones }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer del Modal -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                        <button 
                            wire:click="cerrarModalEstado"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button 
                            wire:click="cambiarEstado"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Cambiar Estado
                        </button>
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