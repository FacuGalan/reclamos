<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800" >
        <div class="min-h-screen flex flex-col">

        <flux:header class="border-b py-8 border-zinc-200 bg-slate-700 
                        dark:border-zinc-700 dark:bg-slate-700 
                        flex flex-col items-center justify-center
                        md:flex-row md:items-center md:relative" >
            <!-- Imagen: centrada arriba en móvil, izquierda en escritorio -->
            <a href="{{ route('home') }}" class="w-40 mb-2 md:mb-0 md:absolute md:left-4 hover:opacity-80 transition-opacity duration-200">
                <x-app-logo-icon class="w-full" />
            </a>
            
            <!-- Título: centrado abajo en móvil, centrado en escritorio -->
            <h1 class="text-2xl font-bold text-white text-center md:flex-1">
                Atención al ciudadano
            </h1>
        </flux:header>

        <main class="py-6 flex-1 flex items-center justify-center" >
            {!! $slot ?? 'Welcome to your application!' !!}
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-zinc-200 bg-slate-700 dark:border-zinc-700 dark:bg-slate-700 pt-2">
        <div class="container mx-auto px-4 pt-2">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                
                <!-- Bloque izquierda (solo visible en sm o más) -->
                <div class="hidden sm:flex items-start">
                    <div class="text-white">
                        <p class="font-semibold">Municipalidad de Mercedes</p>
                        <p class="text-sm text-gray-300">Ciudad de todos</p>
                    </div>
                </div>
                
                <!-- Bloque derecha (botón modal) -->
                <div class="flex flex-col md:flex-row items-center gap-4 text-xs">
                    <div x-data="{ open: false }">
                        <!-- Botón para abrir el modal -->
                        <button 
                            @click="open = true" 
                            class="w-full md:w-auto px-4 py-2 md:mb-0 text-white rounded bg-blue-600 hover:bg-blue-500 cursor-pointer inline-flex items-center justify-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6.16-6.16A19.86 19.86 0 0 1 1.08 4.18 2 2 0 0 1 3 2h3 
                                a2 2 0 0 1 2 1.72c.12.66.33 1.31.63 1.92a2 2 0 0 1-.23 1L7.1 8.1a16 16 0 0 0 8.8 8.8l1.43-1.43 a2 2 0 0 1 1-.23c.61.3 
                                1.26.51 1.92.63A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            Teléfonos Útiles
                        </button>

                        <!-- Modal -->
                        <div 
                            x-show="open" 
                            class="fixed inset-0 flex items-start justify-center pt-20 bg-gray-100/10 dark:bg-gray-900/50 backdrop-blur-md z-50"
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

            <!-- Copyright -->
            <p class="text-gray-400 text-xs text-center mt-2 sm:mt-0 mb-0 pb-3">
                © {{ date('Y') }} Municipalidad de Mercedes. Todos los derechos reservados.
            </p>
        </div>
    </footer>
        </div>
        @stack('scripts')
        @fluxScripts
    </body>
</html>