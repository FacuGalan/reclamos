<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <!-- CSS de SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <!-- JavaScript de SweetAlert2 - AGREGAR ESTA LÍNEA -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-[#314158] dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('reclamos') }}" class="-mb-6 -mt-2 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

       
            <div class="bg-[#3F516A] rounded-lg p-2 mt-4">   
                <flux:navlist.group :heading="__('Menú')" class="grid">
                    <flux:navlist.item class="flux-nav-custom" icon="home" :href="route('reclamos')" :current="request()->routeIs('reclamos')" wire:navigate>
                        <span class="flex items-center justify-between w-full">
                            <span>Reclamos</span>
                            <livewire:contador-notificaciones-reclamos />
                        </span>
                    </flux:navlist.item>
                    
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Mantenimiento')" class="grid">
                    <flux:navlist.item class="flux-nav-custom" icon="building-office-2" :href="route('secretarias')" :current="request()->routeIs('secretarias')" wire:navigate>Secretarías</flux:navlist.item>
                    <flux:navlist.item class="flux-nav-custom" icon="building-office" :href="route('areas')" :current="request()->routeIs('areas')" wire:navigate>Áreas</flux:navlist.item>
                    <flux:navlist.item class="flux-nav-custom" icon="arrows-right-left" :href="route('tipos-movimiento')" :current="request()->routeIs('tipos-movimiento')" wire:navigate>Tipos de Movimiento</flux:navlist.item>
                    <flux:navlist.item class="flux-nav-custom" icon="adjustments-horizontal" :href="route('estados')" :current="request()->routeIs('estados')" wire:navigate>Estados</flux:navlist.item>
                </flux:navlist.group>
            </div>
                  

            <flux:spacer />


            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                    class="cursor-pointer bg-[#3F516A] dark:bg-[#1F2937] dark:text-white"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->dni }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Configuración') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Cerrar sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <script>
            window.addEventListener('mensaje-confirma', (event) => {
                console.log(event);
                Swal.fire({
                    title: event.detail[0].title || '',
                    text: event.detail[0].text || '',
                    icon: event.detail[0].icon || null,
                    iconColor: event.detail[0].iconColor || 'blue' ,
                    showCancelButton: event.detail[0].showCancelButton || true,
                    confirmButtonColor: event.detail[0].confirmButtonColor || false,
                    cancelButtonColor: event.detail[0].cancelButtonColor || '#d33',
                    confirmButtonText: event.detail[0].confirmButtonText || 'Ok',
                    cancelButtonText: event.detail[0].cancelButtonText || 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(event.detail[0].evento,  [event.detail[0].id || null] );
                    }
                });
            });
            
            window.addEventListener('mensaje-toast', (event) => {
                Swal.fire({
                    title: event.detail[0].title || '',
                    text: event.detail[0].text  || '',
                    icon: event.detail[0].icon  || '',
                    iconColor: event.detail[0].iconColor  || '',
                    toast: event.detail[0].toast  || true,
                    position: event.detail[0].position  || 'top-end',
                    showConfirmButton: event.detail[0].showConfirmButton || false,
                    timer: event.detail[0].timer || 3000
                });
            });
            window.addEventListener('mensaje-error', (event) => {
                Swal.fire({
                    title: event.detail[0].title || 'Error',
                    text: event.detail[0].text || 'Ha ocurrido un error',
                    icon: event.detail[0].icon || 'error',
                    confirmButtonColor: event.detail[0].confirmButtonColor || 'blue',
                });
            });

            window.addEventListener('reclamo-guardado-con-redirect', (event) => {
                if (typeof Swal !== 'undefined') {
                    // Mostrar el toast
                    Swal.fire({
                        title: '',
                        text: event.detail[0].mensaje,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Después de que termine el toast, redirigir
                        window.location.href = event.detail[0].redirect_url;
                    });
                } else {
                    // Si no hay SweetAlert, redirigir inmediatamente
                    window.location.href = event.detail[0].redirect_url;
                }
            });
        </script>

        @fluxScripts
    </body>
</html>
