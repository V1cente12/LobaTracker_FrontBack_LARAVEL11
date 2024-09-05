<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Player;
use App\Models\Score;

class GameView extends Component
{
    public $game;
    public $players;
    public $showToReportPointsModal = false;
    public $selectedPlayerId;
    public $selectedGameId;
    public $points;

    public function mount($gameId)
    {
        $this->game = Game::findOrFail($gameId);
        $this->players = Player::where('game_id', $this->game->id)
                                ->with('scores') // Cargar los scores
                                ->get();
    }

    public function render()
    {
        return view('livewire.game-view');
    }

    public function showReportPointsModal($playerId)
    {
        $this->selectedPlayerId = $playerId;
        $this->selectedGameId = $this->game->id; // Establecer el ID del juego actual
        $this->points = null; // Limpiar el campo de puntos
        $this->showToReportPointsModal = true;
    }

    public function hideReportPointsModal()
    {
        $this->showToReportPointsModal = false;
    }

    public function reportPoints()
    {
        $this->validate([
            'points' => 'required|numeric|min:0',
        ]);

        // Crear un nuevo registro en la tabla `scores`
        Score::create([
            'player_id' => $this->selectedPlayerId,
            'game_id' => $this->selectedGameId,
            'points' => $this->points,
        ]);

        // Actualizar el total de puntos en la tabla `players`
        $player = Player::where('id', $this->selectedPlayerId)
                        ->where('game_id', $this->selectedGameId)
                        ->first();

        if ($player) {
            $totalPoints = Score::where('player_id', $this->selectedPlayerId)
                                ->where('game_id', $this->selectedGameId)
                                ->sum('points');

            $player->totalpoints = $totalPoints;
            $player->save();
        }

        $this->hideReportPointsModal();
    }
}
