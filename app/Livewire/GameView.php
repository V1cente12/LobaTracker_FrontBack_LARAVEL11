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
    public $player;
    public $showToReportPointsModal = false;
    public $selectedPlayerId;
    public $selectedGameId;
    public $points;

    public function mount($gameId){
        $this->game = Game::findOrFail($gameId);
        $this->players = Player::where('game_id', $this->game->id)
                                ->with('scores') // Cargar los scores
                                ->get();
    }

    public function showReportPointsModal($gameId){
        $this->player = Player::where('user_id', auth()->id())
                                ->where('game_id', $gameId)
                                ->first(); // posible cambio en prod xq un usuario y un id puede ser igual y el first no funcionaria     
        $this->selectedGameId = $gameId;
        $this->selectedPlayerId =  $this->player->id;
        $this->points = null;
        $this->showToReportPointsModal = true;
    }

    public function hideReportPointsModal(){
        $this->showToReportPointsModal = false;
    }

    public function reportPoints(){
        $this->validate([
            'points' => 'required|numeric|min:0',
        ]);

        // Crear un nuevo registro en la tabla `scores`
        $createPointsScore = Score::create([
            'player_id' => $this->selectedPlayerId,
            'game_id' => $this->selectedGameId,
            'points' => $this->points,
        ]);

        $totalPoints = Score::where('player_id', $this->selectedPlayerId)
                                    ->where('game_id', $this->selectedGameId)
                                    ->sum('points');
        $createPointsScore->total = $totalPoints;
        $createPointsScore->save();
        // Actualizar el total de puntos en la tabla `players`
        $selectedPlayer = Player::where('id', $this->selectedPlayerId)
                        ->where('game_id', $this->selectedGameId)
                        ->first();

        if ($selectedPlayer) {
            $selectedPlayer->total_points = $totalPoints;
            $selectedPlayer->save();
        }
        $this->hideReportPointsModal();
    }

    public function render(){
        return view('livewire.game-view');
    }
}
