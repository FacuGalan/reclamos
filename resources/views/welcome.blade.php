<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header class="border-b pt-10 pb-10 border-zinc-200 bg-slate-700 
                        dark:border-zinc-700 dark:bg-slate-700 flex flex-col justify-center items-center">
            <x-app-logo-icon class="mb-2" />
            <h1 class="text-3xl font-bold text-white">
                Atención al ciudadano
            </h1>
        </flux:header>



        <main class="flex items-center justify-center" >
            {!! $slot ?? 'Welcome to your application!' !!}
        </main>

        <footer class="border-t border-zinc-200 bg-slate-700 dark:border-zinc-700 dark:bg-slate-700 py-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <img  src="{{ asset('logo-muni-m.svg') }}" alt="Logo Municipalidad" class="w-16 h-16">
                        <div class="text-white">
                            <p class="font-semibold">Municipalidad de Mercedes</p>
                            <p class="text-sm text-gray-300">Ciudad de todos</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                        <!--div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Horarios de atención</p>
                            <p class="text-gray-300 text-xs">Lunes a Viernes: 8:00 - 16:00hs</p>
                        </div-->
                        <div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Contacto</p>
                            <p class="text-gray-300 text-xs">📞 (02324) 421370</p>
                        </div>
                        <div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Línea 911</p>
                            <p class="text-gray-300 text-xs">Atención ciudadana</p>
                        </div>
                        <div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Línea 147</p>
                            <p class="text-gray-300 text-xs">Policía</p>
                        </div>
                        <div class="text-center md:text-left">
                            <p class="text-white text-sm font-medium">Línea 107</p>
                            <p class="text-gray-300 text-xs">SAME</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-600 mt-6 pt-4 text-center">
                    <p class="text-gray-400 text-xs">
                        © {{ date('Y') }} Municipalidad de Mercedes. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>