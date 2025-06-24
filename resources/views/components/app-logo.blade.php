<div class="flex aspect-square size-14 items-center justify-center rounded-md p-0 m-0" >
    <img {{ $attributes }} src="{{ asset('logo-muni-M.svg') }}" alt="Logo Municipalidad" class="h-full">
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-none font-semibold">Reclamos 147</span>
    @auth
        <span class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->name }}</span>
    @else
        <span class="text-xs text-zinc-500 dark:text-zinc-400 truncate">(No autenticado)</span>
    @endauth
</div>
