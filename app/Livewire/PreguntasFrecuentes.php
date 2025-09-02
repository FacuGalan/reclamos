<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pregunta;
use App\Models\Area;

class PreguntasFrecuentes extends Component
{
    public $preguntas = [];
    public $search = '';
    public $mostrarTodas = false;
    
    public function mount()
    {
        $this->preguntas = Pregunta::with('area')->get();
    }

    public function updatedSearch()
    {
        // Filtrar preguntas en vivo según lo que escribe el usuario
        $searchTerm = '%' . $this->search . '%';

        $this->preguntas = Pregunta::with('area')
            ->where('pregunta', 'like', $searchTerm)
            ->orWhere('respuesta', 'like', $searchTerm)
            ->orWhereHas('area', function($query) use ($searchTerm) {
                $query->where('nombre', 'like', $searchTerm);
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.preguntas-frecuentes');
    }
}