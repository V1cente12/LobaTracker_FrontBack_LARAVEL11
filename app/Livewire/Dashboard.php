<?php

namespace App\Livewire;

use App\Models\GameType;
use Livewire\Component;

class Dashboard extends Component
{
    public $gameTypes;

    // El método mount se ejecuta cuando se instancia el componente
    public function mount()
    {
        // Cargar todos los tipos de juegos desde la base de datos
        $this->gameTypes = GameType::all();
    }

    public function render()
    {
        // Pasar los tipos de juegos a la vista
        return view('livewire.dashboard', [
            'gameTypes' => $this->gameTypes,
        ]);
    }
    public function goToGameLobby($id)
    {
        return redirect()->route('gamelobby.show', ['id' => $id]);
    }
}
