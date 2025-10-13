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

        <div class="overflow-x-auto">
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
                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-700
                                transition-colors
                            ">
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

        @if($mostrarModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" 
                wire:key="modal-{{ $reporte->id ?? 'empty' }}" 
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div 
                    wire:click="cerrarModal" 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                    aria-hidden="true">
                </div>

                <!-- Modal -->
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto z-10" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg mt-2 leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Detalle del Reporte #{{ $reporteSeleccionado->id ?? '' }}
                                    </h3>
                                    
                                    @if($reporteSeleccionado)

                                    <div class="mt-4 space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"> 
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha del incidente:</label>
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($reporteSeleccionado->fecha)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría:</label>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->categoria->nombre }}</p>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solicitante:</label>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                {{ $reporteSeleccionado->persona ? $reporteSeleccionado->persona->nombre : 'Anónimo' }}
                                                @if($reporteSeleccionado->persona && $reporteSeleccionado->persona->dni)
                                                    <br><span class="text-gray-500 dark:text-gray-400">DNI: {{ $reporteSeleccionado->persona->dni }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección:</label>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->domicilio->direccion }}</p>
                                            @if($reporteSeleccionado->domicilio && $reporteSeleccionado->domicilio->entre_calles)
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Entre calles: {{ $reporteSeleccionado->domicilio->entre_calles }}</p>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción:</label>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->observaciones }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Es habitual:</label>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $reporteSeleccionado->habitual == 1 ? 'Sí' : 'No'}}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-4 sm:flex sm:flex-row-reverse">
                            <button 
                                wire:click="cerrarModal" 
                                type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Paginación -->
        @if($reportes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $reportes->links() }}
            </div>
        @endif
    </div>
</div>
