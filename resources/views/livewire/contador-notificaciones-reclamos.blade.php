<div wire:poll.10s="polling" 
     x-data="{ 
        pulso: false,
        init() {
            $wire.on('nuevo-reclamo-detectado', () => {
                this.pulso = true;
                setTimeout(() => this.pulso = false, 2000);
            });
        }
     }">
    @if($conteoReclamos > 0)
        <span class=" inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] h-5 transition-all duration-300"
              :class="{ 'animate-bounce': pulso }"
              title="Reclamos sin responsable asignado - Última actualización: {{ $ultimaActualizacion }}">
            {{ $conteoReclamos > 999 ? '999+' : $conteoReclamos }}
        </span>
    @endif
</div>