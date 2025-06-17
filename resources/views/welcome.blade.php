<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header class="border-b pt-3 pb-3 border-zinc-200 bg-slate-700 
                        dark:border-zinc-700 dark:bg-slate-700 flex flex-col justify-center items-center" >
            <x-app-logo-icon class="mb-2" />
            <h1 class="text-2xl font-bold text-white">
                Atención al ciudadano
            </h1>
        </flux:header>



        <main class="flex items-center justify-center" >
            {!! $slot ?? 'Welcome to your application!' !!}
        </main>

        <footer class="border-t border-zinc-200 bg-slate-700 dark:border-zinc-700 dark:bg-slate-700 pt-2">
            <div class="container mx-auto px-4 pt-2">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img  src="{{ asset('logo-muni-m.svg') }}" alt="Logo Municipalidad" class="w-16 h-16">
                        <div class="text-white">
                            <p class="font-semibold">Municipalidad de Mercedes</p>
                            <p class=" text-sm text-gray-300">Ciudad de todos</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 text-xs">
                        <!--div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Horarios de atención</p>
                            <p class="text-gray-300 text-xs">Lunes a Viernes: 8:00 - 16:00hs</p>
                        </div-->
                        <div class="text-center">
                            <p class="text-white font-medium">Contacto</p>
                            <p class="text-gray-300">📞 (02324) 421370</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-medium">Línea 911</p>
                            <p class="text-gray-300">Atención ciudadana</p>
                        </div>
                        <div class="text-center">
                            <p class="text-white font-medium">Línea 147</p>
                            <p class="text-gray-300">Policía</p>
                        </div>
                        <div class="text-center md:text-left">
                            <p class="text-white font-medium">Línea 107</p>
                            <p class="text-gray-300">SAME</p>
                        </div>
                    </div>
                </div>
    
                <p class="text-gray-400 text-xs text-center pb-0">
                    © {{ date('Y') }} Municipalidad de Mercedes. Todos los derechos reservados.
                </p>

            </div>
        </footer>

        @fluxScripts
    </body>
</html>