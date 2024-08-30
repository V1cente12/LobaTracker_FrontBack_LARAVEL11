<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;

class Dashboard extends Component
{
    public $games;
    public $showModal = false; // Para mostrar u ocultar el modal
    public $gameName;
    public $initialPrice;
    public $rejoinPrice;

    public function mount()
    {
        $this->games = Game::with('creator')->where('status', 'in_progress')->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function createGame()
    {
        $this->validate([
            'gameName' => 'required|string|max:255',
            'initialPrice' => 'required|numeric|min:0',
            'rejoinPrice' => 'required|numeric|min:0',
        ]);

        Game::create([
            'name' => $this->gameName,
            'initial_price' => $this->initialPrice,
            'rejoin_price' => $this->rejoinPrice,
            'created_by' => auth()->id(),
            'status' => 'in_progress',
        ]);

        $this->reset();
        $this->showModal = false;
        $this->games = Game::where('status', 'active')->get();
    }

    public function showCreateGameModal()
    {
        $this->showModal = true;
    }
}
