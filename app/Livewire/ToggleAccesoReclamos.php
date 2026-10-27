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

        // Recargar la página actual (sin query string, para no arrastrar página ni
        // filtros del modo anterior) así se actualizan listado, filtros y menú.
        // Antes se recargaba con un listener JS en 'livewire:init', que no se
        // registraba al llegar a la página por wire:navigate.
        // Solo se toma el path del Referer, así nunca redirige a otro dominio.
        $path = parse_url((string) request()->header('Referer'), PHP_URL_PATH);
        $this->redirect($path ? request()->getSchemeAndHttpHost() . $path : route('reclamos'));
    }

    public function render()
    {
        return view('livewire.toggle-acceso-reclamos');
    }
}
