<div class="w-full m-0 p-0">
    <!-- Sección Hero: imagen en desktop, solo título en mobile -->
    <div class="w-full relative mb-10">
        <!-- Imagen de fondo (solo desktop) -->
        <img src="{{ asset('fotos/Recurso 15modif.png') }}" 
            alt="Foto 1" 
            class="hidden sm:block w-full object-cover m-0 p-0">

        <!-- Texto superpuesto (desktop) -->
        <div class="hidden sm:flex absolute inset-0 items-center justify-center">
            <div class="w-full max-w-6xl flex justify-between sm:px-18">
                <!-- Texto -->
                <div class="flex flex-col items-end text-left pr-4">
                    <div class="flex flex-col items-start">
                        <h1 class="text-white mb-0 text-3xl sm:text-5xl font-black drop-shadow-lg leading-tight">
                            ATENCIÓN
                        </h1>
                        <h1 class="text-white -mt-4 text-3xl sm:text-5xl font-black drop-shadow-lg leading-tight">
                            CIUDADANA
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col p-3 lg:flex-row gap-12 justify-center items-stretch max-w-5xl mx-auto">
        {{-- Tarjeta 1: Reclamos --}}

        {{-- VERSION DESKTOP (se ve desde sm en adelante) --}}
        <div class="relative hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-xs mx-auto lg:mx-0 flex-col">
            <!-- Imagen -->
            <div class="h-36 flex items-center justify-center bg-[#77BF43] relative">
                <img src="{{ asset('fotos/Recurso 14.png') }}" alt="Foto 1" class="w-full h-full object-cover">
            </div>

            <!-- Círculo flotante mitad/mitad -->
            <div class="absolute left-5 top-[9rem] -translate-y-1/2 w-12 h-12 rounded-full overflow-hidden z-10">
                <img 
                    src="{{ asset('fotos/Recurso 3.png') }}" 
                    alt="" 
                    class="w-full h-full object-cover"
                >
            </div>

            <!-- Texto -->
            <div class="p-3 flex flex-col flex-1">
                <h3 class="text-lg mt-3 font-semibold text-gray-800 dark:text-white mb-2">
                    Reclamos
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 flex-1">
                    Presenta reclamos sobre servicios públicos, infraestructura urbana y otros temas municipales.
                </p>
                <a 
                    href="/nuevo-reclamo" 
                    wire:navigate
                    class="w-32 h-6 px-2 py-1 text-xs font-extrabold bg-[#bed630] hover:bg-[#cdea29] rounded-full flex items-center justify-center transition-colors duration-300">
                    Realizar reclamo
                </a>
            </div>
        </div>

        {{-- VERSION MOBILE (visible solo hasta sm) --}}
        <a href="/nuevo-reclamo" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Ícono circular izquierdo -->
            <div class="w-32 h-full flex items-center justify-center bg-[#bed630]">
                <div class="w-16 h-16 rounded-full overflow-hidden">
                    <img 
                        src="{{ asset('fotos/Recurso 3.png') }}" 
                        alt="" 
                        class="w-full h-full object-cover"
                    >
                </div>
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
        <div class="relative hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-xs mx-auto lg:mx-0 flex-col">
            <!-- Imagen -->
            <div class="h-36 flex items-center justify-center bg-white relative">
                <img src="{{ asset('fotos/Recurso 13.png') }}" alt="Foto 1" class="w-full h-full object-cover object-center">
            </div>

            <!-- Círculo flotante mitad/mitad -->
            <div class="absolute left-5 top-[9rem] -translate-y-1/2 w-12 h-12 rounded-full overflow-hidden z-10">
                <img 
                    src="{{ asset('fotos/Recurso 2.png') }}" 
                    alt="" 
                    class="w-full h-full object-cover"
                >
            </div>

            <!-- Texto -->
            <div class="p-3 flex flex-col flex-1">
                <h3 class="text-lg mt-3 font-semibold text-gray-800 dark:text-white mb-2">
                    Reportes
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 flex-1">
                    Presenta un reporte de seguridad, anónimos o con identificación.
                </p>
                <a 
                    href="/nuevo-reclamo" 
                    wire:navigate
                    class="w-32 h-6 px-2 py-1 text-xs font-extrabold bg-[#91d5e2] hover:bg-[#8fe2f3] rounded-full flex items-center justify-center transition-colors duration-300">
                    Realizar reporte
                </a>
            </div>
        </div>

        {{-- VERSION MOBILE (visible solo hasta sm) --}}
        <a href="/nuevo-reporte" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Ícono circular izquierdo -->
            <div class="w-32 h-full flex items-center justify-center bg-[#91d5e2]">
                <div class="w-16 h-16 rounded-full overflow-hidden">
                    <img 
                        src="{{ asset('fotos/Recurso 2.png') }}" 
                        alt="" 
                        class="w-full h-full object-cover"
                    >
                </div>
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
        <div class="relative hidden sm:flex bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-zinc-700 flex-1 max-w-xs mx-auto lg:mx-0 flex-col">
            <!-- Imagen -->
            <div class="h-36 flex items-center justify-center bg-[#77BF43] relative">
                <img src="{{ asset('fotos/Recurso 12.png') }}" alt="Foto 1" class="w-full h-full object-cover">
            </div>

            <!-- Círculo flotante mitad/mitad -->
            <div class="absolute left-5 top-[9rem] -translate-y-1/2 w-12 h-12 rounded-full overflow-hidden z-10">
                <img 
                    src="{{ asset('fotos/Recurso 1.png') }}" 
                    alt="" 
                    class="w-full h-full object-cover"
                >
            </div>

            <!-- Texto -->
            <div class="p-3 flex flex-col flex-1">
                <h3 class="text-lg mt-3 font-semibold text-gray-800 dark:text-white mb-2">
                    Preguntas Frecuentes
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 flex-1">
                    Accede a información sobre trámites, horarios de atención y contactos útiles.
                </p>
                <a 
                    href="/nuevo-reclamo" 
                    wire:navigate
                    class="w-32 h-6 px-2 py-1 text-xs font-extrabold bg-[#77bf43] hover:bg-[#88dc4c] rounded-full flex items-center justify-center transition-colors duration-300">
                    Ver información
                </a>
            </div>
        </div>

        {{-- VERSION MOBILE (visible solo hasta sm) --}}
        <a href="/tramites" wire:navigate
            class="sm:hidden flex items-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden h-32 pr-4 mx-4 transition hover:bg-gray-100 dark:hover:bg-zinc-700">      
            <!-- Ícono circular izquierdo -->
            <div class="w-32 h-full flex items-center justify-center bg-[#77bf43]">
                <div class="w-16 h-16 rounded-full overflow-hidden">
                    <img 
                        src="{{ asset('fotos/Recurso 1.png') }}" 
                        alt="" 
                        class="w-full h-full object-cover"
                    >
                </div>
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