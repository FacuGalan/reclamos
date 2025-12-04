<div class="max-w-7xl mx-auto p-2 pt-0 space-y-6" >

    @if($currentView === 'list')
        <!-- Vista de Lista de Reclamos -->
        
        <!-- Header Optimizado -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-2 mt-0 pt-0">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Reclamos</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-gray-600 dark:text-gray-300">
                        Administra y da seguimiento a todos los reclamos del sistema
                    </p>
                    
                    @if(count($areas) > 0)
                        <!-- Indicador de áreas con tooltip -->
                        <div class="relative group cursor-pointer">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 cursor-help">
                                📋 {{ count($areas) }} área{{ count($areas) > 1 ? 's' : '' }} asignada{{ count($areas) > 1 ? 's' : '' }}
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            
                            <!-- Tooltip hacia abajo -->
                            <div class="absolute top-full left-0 mt-2 p-3 bg-gray-900 text-white text-sm rounded-lg shadow-lg z-50 w-96 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 cursor-default">
                                <div class="font-medium mb-2 cursor-default">Áreas asignadas:</div>
                                
                                <!-- Lista de áreas con scroll si es muy larga -->
                                <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800 cursor-default">
                                    <ul class="space-y-1">
                                        @foreach($areas as $area)
                                            <li class="flex items-center text-xs py-1 cursor-default">
                                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-2 flex-shrink-0"></span>
                                                <span class="leading-relaxed cursor-default">{{ $area->nombre }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <!-- Contador al final si hay muchas áreas -->
                                @if(count($areas) > 10)
                                    <div class="border-t border-gray-700 mt-2 pt-2 text-xs text-gray-400 cursor-default">
                                        Total: {{ count($areas) }} áreas
                                    </div>
                                @endif
                                
                                <!-- Flecha del tooltip apuntando hacia arriba -->
                                <div class="absolute bottom-full left-4 w-0 h-0 border-l-4 border-r-4 border-b-4 border-l-transparent border-r-transparent border-b-gray-900"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Botón con ancho fijo para evitar que se achique -->
            @if(Auth::user()->rol->lReclamosAlta)
                <div class="flex-shrink-0">
                    @if(Auth::user()->rol->id == 1)
                        <div class="relative group">
                            <!-- Botón Principal con ancho mínimo -->
                            <button 
                                onclick="window.location.href='{{ route('reclamos.create') }}'"
                                class="min-w-[180px] px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <span class="whitespace-nowrap">Nuevo Reclamo</span>
                                <!-- Flecha del dropdown -->
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform scale-95 group-hover:scale-100">
                                
                                <!-- Opción 1: Reclamo Normal -->
                                <a 
                                    href="{{ route('reclamos.create') }}"
                                    wire:navigate
                                    class="w-full px-4 py-3 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-3 transition-colors rounded-t-lg">
                                    <svg class="w-5 h-5 text-[#77BF43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium">Reclamo</span>
                                </a>
                                
                                <!-- Separador -->
                                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                <!-- Opción 2: Reclamo Privado -->
                                <a 
                                    href="{{ route('reclamos.create.interno') }}"
                                    wire:navigate
                                    class="w-full px-4 py-3 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-3 transition-colors rounded-b-lg">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span class="font-medium">Reclamo Interno</span>
                                </a>
                            </div>
                        </div>
                    @else
                        @if(Auth::user()->ver_privada)
                            <button 
                                onclick="window.location.href='{{ route('reclamos.create.interno') }}'"
                                class="min-w-[180px] px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <span class="whitespace-nowrap">Nuevo Reclamo</span>
                            </button>
                        @else   
                            <button 
                                onclick="window.location.href='{{ route('reclamos.create') }}'"
                                class="min-w-[180px] px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <span class="whitespace-nowrap">Nuevo Reclamo</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Filtros -->
        <div x-data="{ mostrarMas: false }" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 py-2 mb-2">
           <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-2 space-y-4 lg:space-y-0">
                <!-- Columna 1: Título (izquierda en desktop, arriba en móvil) -->
                <div class="flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white text-center lg:text-left">Filtros {{ $filtrosActivos ? "($filtrosActivos activo" . ($filtrosActivos > 1 ? 's' : '') . ")" : '' }}</h3> 
                    
                </div>
                
                <!-- Columna 2: Contadores y área (centro) -->
                <div class="flex-1 flex flex-col items-center justify-center space-y-3">
                    <!-- Contadores - en columna en móvil, en fila en desktop -->
                    <div class="flex flex-col sm:flex-row items-center sm:space-x-4 space-y-2 sm:space-y-0">
                        <!-- Total de Reclamos -->
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600 text-sm"><strong>Total:</strong></span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium text-sm">
                                {{ number_format($contadorTotales) }}
                            </span>
                        </div>
                        
                        <!-- Reclamos Pendientes -->
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600 text-sm"><strong>Sin procesar:</strong></span>
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full font-medium text-sm">
                                {{ number_format($contadorSinProcesar) }}
                            </span>
                        </div>
                        
                        <!-- Reclamos en Trámite -->
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600 text-sm"><strong>Procesado:</strong></span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-medium text-sm">
                                {{ number_format($contadorTotales - $contadorSinProcesar) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Área (debajo de los contadores si existe) -->
                    @if($filtro_area)
                        <div class="text-gray-500 text-xs text-center">
                            <span>Área:</span> 
                            <span>{{ $areas->where('id', $filtro_area)->first()->nombre ?? 'Seleccionada' }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Columna 3: Botones (derecha en desktop, abajo en móvil) -->
                <div class="flex-shrink-0 flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <!-- Dropdown de exportación -->
                    <div x-data="{ open: false }" class="relative w-full sm:w-auto">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            class="w-full sm:w-auto px-4 py-2 bg-[#217346] hover:bg-[#2e8b5c] text-white rounded-lg transition-colors cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>Exportar Excel</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <!-- Opción por defecto: exportar todo -->
                                <button
                                    wire:click="exportarExcel"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium">Exportar Todo</span>
                                </button>

                                @if($modelosExportacion->count() > 0)
                                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                    @foreach($modelosExportacion as $modelo)
                                        <button
                                            wire:click="exportarExcel({{ $modelo->id }})"
                                            @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ $modelo->nombre }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ count($modelo->campos) }} campos</div>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                @endif

                                @if(Auth::user()->rol_id <= 3)
                                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                    <a
                                        href="{{ route('admin-modelos-exportacion') }}"
                                        class="w-full text-left px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Administrar Modelos</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button
                        wire:click="limpiarFiltros"
                        class="w-full sm:w-auto px-4 py-2 bg-[#314158] hover:bg-[#4A5D76] text-white rounded-lg transition-colors cursor-pointer text-sm">
                        Limpiar Filtros
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">

                <!-- Búsqueda por id -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Búsqueda ID</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="busqueda_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $busqueda_id ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}"
                        placeholder="Buscar por ID">
                </div>

                <!-- Búsqueda general -->
                <div >
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Búsqueda</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="busqueda"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $busqueda ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}"
                        placeholder="Buscar por descripción, dirección, DNI o nombre...">
                </div>

                @if($this->ver_privada)
                    <!-- Filtro por edificio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Edificio</label>
                        <select 
                            wire:model.live="filtro_edificio"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                            {{ $filtro_edificio ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                            <option value="">Todos los edificios</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}">{{ $edificio->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                @else
                    <!-- Filtro por barrio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Barrio</label>
                        <select 
                            wire:model.live="filtro_barrio"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                            {{ $filtro_barrio ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                            <option value="">Todos los barrios</option>
                            @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Estado</label>
                    <select 
                        wire:model.live="filtro_estado"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_estado ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por área -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Área</label>
                    <select 
                        wire:model.live="filtro_area"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_area ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Categoría</label>
                    <select 
                        wire:model.live="filtro_categoria"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_categoria ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Fecha desde</label>
                    <input 
                        type="date" 
                        wire:model.live="filtro_fecha_desde"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_fecha_desde ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Fecha hasta</label>
                    <input 
                        type="date" 
                        wire:model.live="filtro_fecha_hasta"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_fecha_hasta ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                </div>

                <!-- Usuario alta -->
                <div x-show="mostrarMas" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Usuario alta</label>
                    <select 
                        wire:model.live="filtro_usuario"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_usuario ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Responsable -->
                <div x-show="mostrarMas" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Usuario último contacto</label>
                    <select 
                        wire:model.live="filtro_responsable"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_responsable ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cuadrilla -->
                <div x-show="mostrarMas" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Cuadrilla</label>
                    <select 
                        wire:model.live="filtro_cuadrilla"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_cuadrilla ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todas las cuadrillas</option>
                        @foreach($cuadrillas as $cuadrilla)
                            <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Urgente -->
                <div x-show="mostrarMas" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-0">Urgente</label>
                    <select 
                        wire:model.live="filtro_urgente"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white
                        {{ $filtro_urgente !== '' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}">
                        <option value="">Todos</option>
                        <option value="1">Urgentes</option>
                        <option value="0">No urgentes</option>
                    </select>
                </div>
            </div>

            <!-- Botón ver más -->
            <div class="mt-2 flex items-center">
                <!-- Línea izquierda -->
                <div class="flex-1 border-t border-gray-300 mx-4"></div>

                <!-- Botón centrado -->
                <button 
                    type="button" 
                    @click="mostrarMas = !mostrarMas"
                    class="mx-3 text-black text-sm font-medium cursor-pointer transition-all duration-300 flex items-center space-x-1"
                >
                    <span x-show="!mostrarMas" class="flex items-center space-x-1 bg-gray-100 border border-gray-300 px-2 py-1 rounded hover:bg-gray-200">
                        <span>Búsqueda avanzada</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                    <span x-show="mostrarMas" class="flex items-center space-x-1 bg-gray-100 border border-gray-300 px-2 py-1 rounded hover:bg-gray-200">
                        <span>Búsqueda avanzada</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </span>
                </button>

                <!-- Línea derecha -->
                <div class="flex-1 border-t border-gray-300 mx-4"></div>
            </div>
        </div>




        <!-- Tabla de reclamos -->
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

            <div class=" overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="w-20 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                ID / Fecha
                            </th>
                            <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Solicitante
                            </th>
                            <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                @if(Auth::user()->ver_privada)
                                    Lugar
                                @else
                                    Dirección
                                @endif
                            </th>
                            <th class="w-56 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reclamos as $reclamo)
                            <tr class="
                                    {{ $reclamo->estado_id == 6
    ? 'bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/20 dark:hover:bg-orange-900/30'
    : (is_null($reclamo->responsable_id) 
        ? 'bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/20 dark:hover:bg-blue-900/30' 
        : 'hover:bg-gray-200 dark:hover:bg-gray-700'
    )
}}
                                    transition-colors
                                ">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        #{{ $reclamo->id }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 ">
                                        {{ \Carbon\Carbon::parse($reclamo->fecha)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 ">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white " title="{{ $reclamo->persona->nombre }} {{ $reclamo->persona->apellido }}">
                                        {{ $reclamo->persona->nombre }} {{ $reclamo->persona->apellido }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 ">
                                        DNI: {{ $reclamo->persona->dni }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white" title="{{ $reclamo->categoria->nombre }}">
                                        {{ $reclamo->categoria->nombre }}
                                    </div>
                                    @if (!$reclamo->categoria->privada)
                                        <div class="text-sm text-gray-500 dark:text-gray-400" title="{{ $reclamo->area->nombre }}">
                                            {{ $reclamo->area->nombre }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 " title="{{ Str::before($reclamo->direccion, ',') }}">
                                        @if($reclamo->edificio_id != null)
                                            {{ $reclamo->edificio_id != null ? $reclamo->edificio->nombre : '' }}
                                            <br>
                                            <span class="truncate block">
                                                {{ Str::before($reclamo->direccion, ',') }}
                                            </span>
                                        @else
                                            @if($reclamo->numero_tranquera)
                                                Tranquera: N° {{ $reclamo->numero_tranquera }}
                                                <br>
                                            @endif
                                             {{ strlen(Str::before($reclamo->direccion, ',')." ".$reclamo->direccion_rural) > 50 ? substr(Str::before($reclamo->direccion, ',')." ".$reclamo->direccion_rural, 0, 50) . '...' : Str::before($reclamo->direccion, ',')." ".$reclamo->direccion_rural}}
                                            @if($reclamo->barrio_id > 0)
                                                <br>
                                                <span class="truncate block" title="Barrio: {{ $reclamo->barrio->nombre}}">
                                                    Barrio: {{ $reclamo->barrio->nombre}}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                   <div class="text-sm text-gray-900 dark:text-white" title="{{ $reclamo->descripcion }}">
                                         {{ strlen($reclamo->descripcion) > 150 ? substr($reclamo->descripcion, 0, 150) . '...' : $reclamo->descripcion }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center align-center space-x-2 justify-center">
                                        <livewire:estado-badge :estado-id="$reclamo->estado->id" 
                                                            size="small"
                                                            wire:key="estados-{{$reclamo->id}}-{{ $this->listaTimestamp }}"  />
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center align-center space-x-2 justify-center">
                    
                                        @if(($reclamo->estado_id < 4 || $reclamo->estado_id > 5) && Auth::user()->rol_id != 5 )
                                            <!-- Editar -->
                                            @if(Auth::user()->rol->lReclamosModifica)
                                                <button 
                                                    wire:click="editarReclamo({{ $reclamo->id}},true)"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer"
                                                    title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Eliminar -->
                                            @if(Auth::user()->rol->lReclamosBaja)
                                                <button 
                                                    wire:click="confirmarEliminacion({{ $reclamo->id }})"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                    title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @else

                                            <button 
                                                wire:click="editarReclamo({{ $reclamo->id}}, false)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                                title="Ver">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                        @endif
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
                                        <p class="text-lg font-medium">No se encontraron reclamos</p>
                                        <p class="text-sm">Intenta ajustar los filtros o crear un nuevo reclamo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($reclamos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $reclamos->links() }}
                </div>
            @endif
        </div>

    @elseif($currentView === 'edit')
        
    <div class="mb-6">
            <button 
                wire:click="volverALista"
                class="flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mb-4 cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a la lista
            </button>
        </div>
        
        @if($selectedReclamoId)
            <livewire:modificar-reclamo lazy
                :reclamo-id="$selectedReclamoId"
                :editable="$reclamoEditable"
                :key="'modificar-reclamo-' . $selectedReclamoId" />
        @endif

    @endif

    <!-- Modal de confirmación para eliminar (solo este modal se mantiene) -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModalEliminacion"></div>


                <!-- Modal -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-[9999]">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.349 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Eliminar Reclamo
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ¿Estás seguro de que deseas eliminar el reclamo #{{ $selectedReclamo?->id }}? Esta acción no se puede deshacer.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            wire:click="eliminarReclamo" 
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                        <button 
                            wire:click="cerrarModalEliminacion" 
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>