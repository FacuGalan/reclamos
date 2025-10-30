<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ToggleAccesoReclamos extends Component
{
    public $verPrivada;
    public $tipoAcceso;

    public function mount()
    {
        $this->verPrivada = Auth::user()->ver_privada;
        $this->tipoAcceso = Auth::user()->tipo_acceso_reclamos;
    }

    public function toggleAcceso()
    {
        // Solo permitir toggle si el usuario tiene acceso a ambos (tipo 3)
        if ($this->tipoAcceso !== 3) {
            return;
        }

        // Cambiar el valor de ver_privada
        $this->verPrivada = !$this->verPrivada;

        // Actualizar en la base de datos
        $user = Auth::user();
        $user->ver_privada = $this->verPrivada;
        $user->save();

        // Refrescar la página para aplicar los cambios
        $this->dispatch('acceso-cambiado');
    }

    public function render()
    {
        return view('livewire.toggle-acceso-reclamos');
    }
}
