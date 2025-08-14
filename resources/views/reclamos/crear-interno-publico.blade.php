<div class="container mx-auto p-4">
    {{-- Información del usuario para quien se crea el reclamo --}}
    <!--div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Creando reclamo para: {{ $nombre }} {{ $apellido }}
                </h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    DNI: {{ $dni }}
                </p>
            </div>
        </div>
    </div-->

    {{-- Componente Livewire con datos precargados --}}
    <livewire:alta-reclamo 
        :show-persona-form="true"
        :is-private-area="true"
        :contexto="'externo'"
        :datos-precargados="[
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => '',
            'email' => ''
        ]"
        :redirect-after-save="url()->previous()"
        :success-message="'Reclamo creado exitosamente para ' . $nombre . ' ' . $apellido"
        :key="'reclamo-externo-' . $dni . '-' . now()" />
</div>