<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header class="border-b pt-15 pb-15 border-zinc-200 bg-slate-700 
                        dark:border-zinc-700 dark:bg-slate-700 flex flex-col justify-center items-center">
            <x-app-logo-icon class="mb-2" />
            <h1 class="text-3xl font-bold text-white">
                Atención al ciudadano
            </h1>
        </flux:header>

        <main class="flex items-center justify-center min-h-screen">
            {{ $slot ?? 'Welcome to your application!' }}
        </main>

        @fluxScripts
    </body>
</html>