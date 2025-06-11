{{-- resources/views/home.blade.php --}}
<div class="container mx-auto px-6 py-12" >
 

    <div class="flex flex-col lg:flex-row gap-8 justify-center items-stretch max-w-6xl mx-auto">
        {{-- Tarjeta 1: Reclamos --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0">
            <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Realizar Reclamo
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Presenta reclamos sobre servicios públicos, infraestructura urbana y otros temas municipales.
                </p>
                <a 
                    href="/nuevo-reclamo" 
                    wire:navigate
                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                    Hacer Reclamo
                </a>
            </div>
        </div>

        {{-- Tarjeta 2: Consultar Estado --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0">
            <div class="h-48 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Consultar Estado
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Verifica el estado y seguimiento de los reclamos que has presentado anteriormente.
                </p>
                <a 
                    href="/consultar-reclamo" 
                    wire:navigate
                    class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                    Consultar Estado
                </a>
            </div>
        </div>

        {{-- Tarjeta 3: Información --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0">
            <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Información Municipal
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Accede a información sobre trámites, horarios de atención y contactos útiles.
                </p>
                <a 
                    href="/informacion" 
                    wire:navigate
                    class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                    Ver Información
                </a>
            </div>
        </div>
    </div>
</div>