<x-layouts.app :title="$tipoInterno ? 'Nuevo Reclamo Interno' : 'Nuevo Reclamo'">
    <div class="mb-6">
        <a href="{{ route('reclamos') }}" 
           wire:navigate
           class="flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a la lista
        </a>
    </div>

    <livewire:alta-reclamo 
        :show-persona-form="true" 
        :is-private-area="$tipoInterno"
        :contexto="'privado'"
        :key="'alta-reclamo-' . ($tipoInterno ? 'interno' : 'normal') . '-' . now()" />
</x-layouts.app>