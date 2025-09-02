<div class="container mx-auto">
    <div class="flex flex-col lg:flex-row gap-8 justify-center items-stretch max-w-6xl mx-auto">
        {{-- Tarjeta 1: Reclamos --}}

        {{-- VERSION DESKTOP (se ve desde sm en adelante) --}}
        <div class="hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0 flex flex-col">
            <div class="h-48 flex items-center justify-center" style="background-color: #77BF43;">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Reclamos
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Presenta reclamos sobre servicios públicos, infraestructura urbana y otros temas municipales.
                </p>
                <a 
                    href="/nuevo-reclamo" 
                    wire:navigate
                    class="block w-full text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200 mt-auto"
                    style="background-color: #77BF43;"
                    onmouseover="this.style.backgroundColor='#BED630'"
                    onmouseout="this.style.backgroundColor='#77BF43'">
                    Realizar Reclamo
                </a>
            </div>
        </div>
        {{-- VERSION MOBILE (visible solo hasta sm) --}}
        <a href="/nuevo-reclamo" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Imagen izquierda -->
            <div class="w-32 h-full flex items-center justify-center" style="background-color: #77BF43;">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            <!-- Texto -->
            <div class="w-full ml-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white pt-6">
                    Realizar reclamo    
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Presenta un reclamo a la Municipalidad de Mercedes
                </p>
            </div>
        </a>

        {{-- Tarjeta 2: Reportes --}}

        {{-- VERSION DESKTOP (se ve desde sm en adelante) --}}
        <div class="hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0 flex flex-col">
            <div class="h-48 flex items-center justify-center" style="background-color: #91D5E2;">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Reportes
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Presenta un reporte de seguridad, anónimos o con identificación.
                </p>
                <a 
                    href="/nuevo-reporte" 
                    wire:navigate
                    class="block w-full text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200 mt-auto"
                    style="background-color: #91D5E2;"
                    onmouseover="this.style.backgroundColor='#7BC4D1'"
                    onmouseout="this.style.backgroundColor='#91D5E2'">
                    Realizar reporte
                </a>
            </div>
        </div>

        {{-- VERSION MOBILE (visible solo hasta sm) --}}
         <a href="/nuevo-reporte" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Imagen izquierda -->
            <div class="w-32 h-full flex items-center justify-center" style="background-color: #91D5E2;">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            <!-- Texto -->
            <div class="w-full ml-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white pt-6">
                    Realizar reporte    
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Presenta un reporte a la Municipalidad de Mercedes
                </p>
            </div>
        </a>

        {{-- Tarjeta 3: Guía de trámites --}}

        {{-- VERSION DESKTOP (se ve desde sm en adelante) --}}
        <div class="hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-sm mx-auto lg:mx-0 flex flex-col">
            <div class="h-48 flex items-center justify-center" style="background-color: #BED630;">
                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                    Preguntas Frecuentes
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Accede a información sobre trámites, horarios de atención y contactos útiles.
                </p>
                <a 
                    href="/tramites" 
                    wire:navigate
                    class="block w-full text-white text-center font-medium py-3 px-4 rounded-lg transition-colors duration-200 mt-auto"
                    style="background-color: #BED630;"
                    onmouseover="this.style.backgroundColor='#77BF43'"
                    onmouseout="this.style.backgroundColor='#BED630'">
                    Ver Información
                </a>
            </div>
        </div>

        {{-- VERSION MOBILE (visible solo hasta sm) --}}

        <a href="/tramites" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Imagen izquierda -->
            <div class="w-32 h-full flex items-center justify-center" style="background-color: #BED630;">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            <!-- Texto -->
            <div class="w-full ml-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white pt-6">
                    Preguntas Frecuentes  
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 flex-1">
                    Accede a información útil sobre trámites
                </p>
            </div>
        </a>
    </div>
</div>