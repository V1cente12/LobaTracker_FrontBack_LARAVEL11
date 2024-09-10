<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Player;
use App\Models\Score;
use App\Models\Payments;

class GameView extends Component
{
    public $game;
    public $players;
    public $player;
    public $payments;
    public $showToReportPointsModal = false;
    public $selectedPlayerId;
    public $selectedGameId;
    public $points;

    public function mount($gameId){
        $this->loadGameData($gameId);
    }

    public function showReportPointsModal($gameId){
        $this->initializeReportPointsModal($gameId);
    }

    public function hideReportPointsModal(){
        $this->showToReportPointsModal = false;
    }

    public function reportPoints(){
        $this->validate([
            'points' => 'required|numeric|min:0',
        ]);

        $this->createOrUpdateScore();
        $this->updatePlayerTotalPoints();
        $this->updateGameStatus();

        $this->hideReportPointsModal();
    }

    private function loadGameData($gameId){
        $this->game = Game::findOrFail($gameId);
        $this->players = Player::where('game_id', $this->game->id)
                                ->with('scores')
                                ->get();
        $this->payments = Payments::where('game_id', $gameId)
                                ->sum('amount');
    }

    private function initializeReportPointsModal($gameId){
        $this->player = Player::where('user_id', auth()->id())
                                ->where('game_id', $gameId)
                                ->firstOrFail();    
        $this->selectedGameId = $gameId;
        $this->selectedPlayerId = $this->player->id;
        $this->points = null;
        $this->showToReportPointsModal = true;
    }

    private function createOrUpdateScore(){
        $score = Score::create([
            'player_id' => $this->selectedPlayerId,
            'game_id' => $this->selectedGameId,
            'points' => $this->points,
        ]);

        $totalPoints = Score::where('player_id', $this->selectedPlayerId)
                            ->where('game_id', $this->selectedGameId)
                            ->sum('points');
                            
        $score->total = $totalPoints;
        $score->save();
    }

    private function updatePlayerTotalPoints(){
        $player = Player::where('id', $this->selectedPlayerId)
                        ->where('game_id', $this->selectedGameId)
                        ->firstOrFail();

        $player->total_points = Score::where('player_id', $this->selectedPlayerId)
                                    ->where('game_id', $this->selectedGameId)
                                    ->sum('points');
        $player->save();
    }

    private function updateGameStatus(){
        $game = Game::where('id', $this->selectedGameId)->firstOrFail();

        if ($game->status != 'in_progress') {
            $game->status = 'in_progress';
            $game->save();
        }
    }

    public function render(){
        return view('livewire.game-view');
    }
}
