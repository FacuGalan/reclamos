<div>
    @if($tipoAcceso === 3)
        <div class="fixed bottom-1 right-6 z-50">
            <button
                wire:click="toggleAcceso"
                class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 group">

                <!-- Icono -->
                <div class="relative w-10 h-10 flex items-center justify-center">
                    @if($verPrivada)
                        <!-- Icono de Privado/Interno -->
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    @else
                        <!-- Icono de Público -->
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>

                <!-- Texto -->
                <div class="flex flex-col items-start">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Viendo:</span>
                    <span class="text-sm font-bold {{ $verPrivada ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $verPrivada ? 'INTERNOS' : 'PÚBLICOS' }}
                    </span>
                </div>

                <!-- Flecha de cambio -->
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </button>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('acceso-cambiado', () => {
                    window.location.reload();
                });
            });
        </script>
    @endif
</div>
