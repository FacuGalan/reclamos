<?php

use Livewire\Volt\Component;
use App\Models\Estado;

new class extends Component {
    public $estadoId;
    public $estado;
    public $size = 'normal'; // 'small', 'normal', 'large'

    public function mount($estadoId, $size = 'normal'): void
    {
        $this->estadoId = $estadoId;
        $this->size = $size;

        
        // Cargar el estado desde la base de datos
        $this->estado = Estado::find($estadoId);
    }

    public function getSizeClasses(): string
    {
        return match($this->size) {
            'small' => 'px-2 py-1 text-sm',
            'large' => 'px-4 py-2 text-base',
            default => 'px-2 py-1 text-xs', // normal
        };
    }

}; ?>

<div>
    @if($estado)
        <span 
            class="inline-flex items-center font-bold rounded-full {{ $this->getSizeClasses() }}"
            style="background-color: {{ $estado->codigo_color }}; color: {{ $estado->getColorTexto() }};">   
            {{ $estado->nombre }}
        </span>
    @else
        <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
            Sin estado
        </span>
    @endif
</div>