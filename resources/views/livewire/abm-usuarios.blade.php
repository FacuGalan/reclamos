<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">

    <!-- Notificación flotante -->
    @if($mostrarNotificacion)
        <div 
            x-data="{ visible: true }"
            x-show="visible"
            x-init="setTimeout(() => visible = false, 3000)"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            wire:key="notification-{{ $notificacionTimestamp ?? time() }}"
            class="fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-sm
                @if($tipoNotificacion === 'success') bg-green-100 border border-green-400 text-green-700
                @else bg-red-100 border border-red-400 text-red-700 @endif">
            
            <div class="flex items-center">
                @if($tipoNotificacion === 'success')
                    <svg class="h-6 w-6 mr-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-6 w-6 mr-3 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.354 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                @endif
                
                <div>
                    <p class="font-semibold">{{ $mensajeNotificacion }}</p>
                </div>
                
                <button 
                    @click="visible = false"
                    class="ml-4 @if($tipoNotificacion === 'success') text-green-400 hover:text-green-600 @else text-red-400 hover:text-red-600 @endif">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gestión de Usuarios</h1>
            <p class="text-gray-600 dark:text-gray-300">Administra los usuarios del sistema</p>
        </div>
        
        <button 
            wire:click="nuevoUsuario"
            class="px-6 py-3 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors flex items-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nuevo Usuario
        </button>
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
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Búsqueda general -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Búsqueda</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Buscar por nombre, email o DNI...">
            </div>

            <!-- Filtro por rol -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rol</label>
                <select 
                    wire:model.live="filtro_rol"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
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
        </div>
    </div>

    <!-- Tabla de usuarios -->
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
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            DNI
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Contacto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Áreas Asignadas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-[#77BF43] flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ $usuario->initials() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $usuario->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $usuario->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $usuario->dni }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $usuario->telefono }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($usuario->rol)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $usuario->rol->nombre }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Sin rol</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($usuario->areas as $area)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $area->nombre }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Editar -->
                                    <button 
                                        wire:click="editarUsuario({{ $usuario->id }})"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <!-- Eliminar -->
                                    @if($usuario->id !== auth()->id())
                                        <button 
                                            wire:click="confirmarEliminacion({{ $usuario->id }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                            title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron usuarios</p>
                                    <p class="text-sm">Intenta ajustar los filtros o crear un nuevo usuario.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>


   <!-- Modal de Formulario (Crear/Editar) -->
    @if($showFormModal)
    <!-- Contenedor principal del modal -->
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay de fondo -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 transition-opacity" wire:click="cerrarModal"></div>

        <!-- Contenedor de centrado -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal content -->
            <div class="relative w-full max-w-5xl bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all">
                
                <!-- Header del Modal -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            {{ $isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
                        </h2>
                        <button 
                            wire:click="cerrarModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Contenido del Modal -->
                <form wire:submit.prevent="save">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 max-h-[80vh] overflow-y-auto">
                        <div class="space-y-6">
                            
                            <!-- Contenedor principal para Información Personal y Configuración de Cuenta -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                
                                <!-- Información Personal -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                        Información Personal
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <!-- DNI -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                DNI *
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="dni"
                                                autocomplete="off"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="Ingrese el DNI">
                                            @error('dni') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Nombre -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nombre Completo *
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="name"
                                                autocomplete="off"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="Ingrese el nombre completo">
                                            @error('name') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Email *
                                            </label>
                                            <input 
                                                type="email" 
                                                wire:model="email"
                                                autocomplete="new-email"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="usuario@ejemplo.com">
                                            @error('email') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Teléfono -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Teléfono *
                                            </label>
                                            <input 
                                                type="text" 
                                                wire:model="telefono"
                                                autocomplete="off"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="Ej: 2324421370">
                                            @error('telefono') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuración de Cuenta -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">
                                        Configuración de Cuenta
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <!-- Rol -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Rol
                                            </label>
                                            <select 
                                                wire:model="rol_id"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white">
                                                <option value="">Sin rol específico</option>
                                                @foreach($roles as $rol)
                                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('rol_id') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Contraseña -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Contraseña {{ $isEditing ? '(opcional)' : '*' }}
                                            </label>
                                            <input 
                                                type="password" 
                                                wire:model="password"
                                                autocomplete="new-password"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="{{ $isEditing ? 'Dejar vacío para mantener actual' : 'Mínimo 8 caracteres' }}">
                                            @error('password') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Confirmar Contraseña -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Confirmar Contraseña {{ $isEditing ? '(opcional)' : '*' }}
                                            </label>
                                            <input 
                                                type="password" 
                                                wire:model="password_confirmation"
                                                autocomplete="new-password"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#77BF43] focus:border-[#77BF43] dark:bg-gray-700 dark:text-white"
                                                placeholder="{{ $isEditing ? 'Dejar vacío para mantener actual' : 'Confirme la contraseña' }}">
                                            @error('password_confirmation') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Acceso para ver áreas privadas -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Área Privada
                                            </label>
                                            <div class="w-full py-3 hover:bg-gray-50 dark:hover:bg-gray-700 p-3 rounded-lg transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                                                <div class="flex items-center">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="ver_privada"
                                                        id="acceso_areas_privadas"
                                                        class="h-4 w-4 text-[#77BF43] focus:ring-[#77BF43] border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600 cursor-pointer"
                                                    >
                                                    <label for="acceso_areas_privadas" class="ml-3 text-sm text-gray-700 dark:text-gray-300 select-none cursor-pointer">
                                                        Permitir acceso a áreas privadas
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Asignación de Áreas - Ocupa todo el ancho disponible -->
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                                    Asignación de Áreas *
                                </h3>
                                
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <!-- Área de selección más grande -->
                                    <div class="max-h-64 overflow-y-auto">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($areas as $area)
                                                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-3 rounded-lg transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="areas_asignadas" 
                                                        value="{{ $area->id }}"
                                                        class="form-checkbox h-5 w-5 text-[#77BF43] rounded focus:ring-[#77BF43] border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $area->nombre }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <!-- Contador de áreas seleccionadas -->
                                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Selecciona las áreas a las que el usuario tendrá acceso
                                            </p>
                                            <div 
                                                x-data="{ 
                                                    selectedAreas: @entangle('areas_asignadas'),
                                                    get count() { 
                                                        return Array.isArray(this.selectedAreas) ? this.selectedAreas.length : 0; 
                                                    }
                                                }" 
                                                class="text-sm font-medium text-[#77BF43]">
                                                <span x-text="count"></span> área(s) seleccionada(s)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('areas_asignadas') 
                                    <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
                        <div class="flex items-center justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="cerrarModal"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="px-6 py-2 bg-[#77BF43] text-white rounded-lg hover:bg-[#5a9032] transition-colors cursor-pointer relative disabled:opacity-50">
                                
                                <!-- Estado normal -->
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEditing ? 'Actualizar Usuario' : 'Crear Usuario' }}
                                </span>
                                
                                <!-- Estado guardando -->
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de confirmación para eliminar -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-opacity-80">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.349 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Eliminar Usuario
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            ¿Estás seguro de que deseas eliminar al usuario "{{ $selectedUsuario?->name }}"? Esta acción no se puede deshacer.
                        </p>
                        @if($selectedUsuario)
                            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>DNI:</strong> {{ $selectedUsuario->dni }}<br>
                                    <strong>Email:</strong> {{ $selectedUsuario->email }}<br>
                                    <strong>Áreas asignadas:</strong> {{ $selectedUsuario->areas->count() }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="cerrarModalEliminacion" 
                        type="button" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button 
                        wire:click="eliminarUsuario" 
                        type="button" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>