<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barrio;
use Illuminate\Support\Collection;

class MapaBarrios extends Component
{
    public Collection $barrios;

    public function mount()
    {
        $this->barrios = Barrio::orderBy('nombre')->get();
    }

    public function render()
    {
        return view('livewire.mapa-barrios');
    }
}