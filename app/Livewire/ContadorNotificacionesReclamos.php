<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reclamo;
use Illuminate\Support\Facades\Auth;

class ContadorNotificacionesReclamos extends Component
{
    public $conteoReclamos = 0;
    public $userAreas = [];
    public $ultimaActualizacion = '';

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $this->userAreas = Auth::user()->areas->pluck('id')->toArray();

        // Si el usuario no tiene áreas asignadas, mostrar todas (para casos especiales como admin)
        if (empty($this->userAreas)) {
            $this->userAreas = \App\Models\Area::pluck('id')->toArray();
        }

        $this->actualizarConteo();
    }

    public function actualizarConteo()
    {
        $anteriorConteo = $this->conteoReclamos;
        
        $this->conteoReclamos = Reclamo::whereNull('responsable_id')
            ->whereIn('area_id', $this->userAreas)
            ->count();
            
        $this->ultimaActualizacion = now()->format('H:i:s');
        
        // Si aumentó el conteo, mostrar una pequeña animación
        if ($this->conteoReclamos > $anteriorConteo) {
            $this->dispatch('nuevo-reclamo-detectado');
        }
    }

    // Este método se ejecutará automáticamente cada 30 segundos
    public function polling()
    {
        $this->actualizarConteo();
    }

    public function render()
    {
        return view('livewire.contador-notificaciones-reclamos');
    }
}