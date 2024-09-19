<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Player;
use App\Models\Score;
use App\Models\Payments;
use App\Models\GameType;

class GameView extends Component
{
    public $game;
    public $players;
    public $player;
    public $payments;
    public $showToReportPointsModal = false;
    public $showToLoadingModal      = false;
    public $showToRejoinModal       = false;
    public $showToWinnerModal       = false;
    public $selectedPlayerId;
    public $selectedGameId;
    public $points;

    public function mount($gameId){
        $this->loadGameData($gameId);
    }

    private function loadGameData($gameId){
        $this->game     = Game::findOrFail($gameId);
        $this->players  = Player::where('game_id', $this->game->id)
                                    ->with('scores')
                                    ->get();
        $this->payments = Payments::where('game_id', $gameId)
                                    ->sum('amount');
    }

    public function showReportPointsModal($gameId){
        $this->initializeReportPointsModal($gameId);
    }

    private function initializeReportPointsModal($gameId){
        $this->player                   = Player::where('user_id', auth()->id())
                                                ->where('game_id', $gameId)
                                                ->firstOrFail();    
        $this->selectedGameId           = $gameId;
        $this->selectedPlayerId         = $this->player->id;
        $this->points                   = null;
        $this->showToReportPointsModal  = true;
    }

    public function reportPoints(){
        $this->validate([
            'points' => 'required|numeric|min:0',
        ]);

        $this->createOrUpdateScore();
        $this->updatePlayerTotalPoints();
        $this->updateGameStatus();

        if ($this->allPlayersReported()) {
            $this->prepareForNextRound();
        } else {
            $this->showToLoadingModal = true;
        }

        $this->hideReportPointsModal();
    }

    private function createOrUpdateScore(){

      $currentTurn = $this->getLastTurn();

        $score = Score::create([
            'player_id' => $this->selectedPlayerId,
            'game_id' => $this->selectedGameId,
            'turn' => $currentTurn,
            'points' => $this->points,
        ]);

        $totalPoints = Score::where('player_id', $this->selectedPlayerId)
                            ->where('game_id', $this->selectedGameId)
                            ->sum('points');
                            
        $score->total           = $totalPoints;
        $score->has_reported    = true;
        $score->save();
    }

    public function getLastTurn(){
        $lastTurn = Score::where('game_id', $this->selectedGameId)
        ->where('player_id', $this->selectedPlayerId)
        ->max('turn');
        
        return $currentTurn = $lastTurn ? $lastTurn + 1 : 1;
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

    public function hideReportPointsModal(){
        $this->showToReportPointsModal = false;
    }

    private function allPlayersReported()
    {
        // Obtener el número total de jugadores en el juego
        $totalPlayers = Player::where('game_id', $this->selectedGameId)->count();
    
        // Obtener el último turno registrado en la tabla de scores
        $lastTurn = Score::where('game_id', $this->selectedGameId)->max('turn');
    
        // Contar cuántos jugadores han reportado en la ronda actual (último turno)
        $reportedPlayersCount = Score::where('game_id', $this->selectedGameId)
                                     ->where('turn', $lastTurn)
                                     ->where('has_reported', true)
                                     ->distinct('player_id')
                                     ->count('player_id');
    
        // Verificar si el número de jugadores que han reportado coincide con el número total de jugadores
        return $reportedPlayersCount === $totalPlayers;
    }
    
    public function verifyAllPlayersReported(){
        if ($this->allPlayersReported()) {
            $this->prepareForNextRound();
        } else {
            $this->showToLoadingModal = true; 
        }
    }

    private function prepareForNextRound(){
        $this->showToLoadingModal = false;
    }

    public function render(){
        return view('livewire.game-view');
    }
}
