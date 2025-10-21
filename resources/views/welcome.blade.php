<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 overflow-x-hidden">
    <div class="min-h-screen flex flex-col">

        <!-- HEADER -->
        <flux:header class="py-4 md:py-8 border-zinc-200 bg-[#bed630]
                        dark:border-zinc-700 dark:bg-slate-700
                        flex items-center justify-center
                        md:relative">
            
            <!-- VERSIÓN MOBILE: Logo arriba + Título abajo (todo centrado) -->
            <div class="flex md:hidden flex-col items-center gap-1">
                <a href="{{ route('home') }}" class="w-22 -mt-6 h-22 -mb-4">
                    <img src="{{ asset('fotos/Recurso 16.png') }}" alt="Logo" class="w-full h-full object-contain">
                </a>
                <h1 class="text-white text-xl font-black leading-tight text-center">
                    ATENCIÓN CIUDADANA
                </h1>
            </div>
            
            <!-- VERSIÓN DESKTOP: Solo logo grande a la izquierda -->
            <div class="w-full m-0 p-0 -mt-10 hidden md:block">
                <a href="{{ route('home') }}"
                class=" w-40 md:absolute md:left-67 hover:opacity-80 transition-opacity duration-200">
                    <img src="{{ asset('fotos/Recurso 16.png') }}" alt="">
                </a>
            </div>   
            
        </flux:header>

        <!-- MAIN -->
        <main class="flex-1 flex justify-center w-full ">
            {!! $slot ?? 'Welcome to your application!' !!}
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-zinc-200 bg-zinc-800 dark:border-zinc-700 dark:bg-slate-700 pt-4 pb-4">
            <div class="container mx-auto px-4">
                <!-- Flex contenedor: centrado en móvil, justify-between en md -->
                <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-4">

                    <!-- Bloque izquierda (solo en sm o más) -->
                    <div class="hidden sm:flex items-start md:pl-4">
                        <div class="text-white md:ml-60">
                            <p class="font-semibold text-xs">Municipalidad de Mercedes</p>
                            <div class="flex flex-col md:flex-row gap-2 mt-1">
                                <a href="https://www.facebook.com/munimercedes/?locale=es_LA" target="_blank" rel="noopener noreferrer">
                                    <img class="w-3 h-3" src="{{ asset('fotos/Recurso 11.png') }}" alt="">
                                </a>
                                <a href="https://www.instagram.com/munimercedes/" target="_blank" rel="noopener noreferrer">
                                    <img class="w-3 h-3" src="{{ asset('fotos/Recurso 10.png') }}" alt="">
                                </a>
                                <a href="https://www.youtube.com/channel/UCUZfAvQDlze4sejKUNG-_Zg" target="_blank" rel="noopener noreferrer">
                                    <img class="w-4 h-3" src="{{ asset('fotos/Recurso 9.png') }}" alt="">
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bloque derecha (botón modal) -->
                    <div class="flex flex-col md:flex-row items-center gap-4 text-xs w-full sm:w-auto justify-center md:justify-end md:mr-19">
                        <div x-data="{ open: false }">
                            <!-- Botón para abrir el modal -->
                            <button 
                                @click="open = true" 
                                class="w-38 h-8 md:mr-44 md:w-44 px-2 py-2 rounded-full bg-[#bdd62f] hover:bg-[#b3d010] flex items-center gap-3 cursor-pointer"
                            >
                                <!-- Ícono teléfono -->
                                <img class="w-6 h-6" src="{{ asset('fotos/Recurso 6.png') }}" alt="Teléfono">

                                <!-- Texto -->
                                <span class="flex-1 text-left text-xs font-black">
                                    TELÉFONOS ÚTILES
                                </span>
                            </button>

                            <!-- Modal -->
                            <div 
                                x-show="open" 
                                class="fixed inset-0 flex items-start justify-center pt-10 bg-gray-100/10 dark:bg-gray-900/50 backdrop-blur-md z-50"
                            >
                                <div class="mt-10 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg 
                                            w-full max-w-md md:max-w-lg lg:w-1/3 
                                            max-h-[80vh] flex flex-col">
                                    <!-- Header -->
                                    <h2 class="text-center text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">
                                        Teléfonos Útiles
                                    </h2>

                                    <!-- Contenedor scrollable para la tabla -->
                                    <div class="overflow-y-auto max-h-[60vh]">
                                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-sm">
                                            <thead>
                                                <tr class="bg-lime-500 text-white">
                                                    <th class="px-4 py-2 text-left border-b border-gray-200 dark:border-gray-700">Nombre</th>
                                                    <th class="px-4 py-2 text-left border-b border-gray-200 dark:border-gray-700">Teléfono</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(\App\Models\TelefonoUtil::all() as $tel)
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100">
                                                        {{ $tel->nombre }}
                                                    </td>
                                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100">
                                                        {{ $tel->telefono }}
                                                    </td>
                                                </tr>
                                                @endforeach                                             
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Footer -->
                                    <button 
                                        @click="open = false" 
                                        class="mx-auto mt-4 w-24 py-2 px-4 bg-gray-600 hover:bg-gray-500 text-white rounded cursor-pointer"
                                    >
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </footer>

    </div>
    @stack('scripts')
    @fluxScripts
</body>
</html>
