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
    public $paymentsByPlayer;
    public $showToReportPointsModal = false;
    public $showToLoadingModal      = false;
    public $showToRejoinModal       = false;
    public $showToWinnerModal       = false;
    public $showToLeaveGameModal    = false;
    public $showToExactDealModal      = false;
    public $selectedPlayerId;
    public $selectedGameId;
    public $points;
    public $winnerName;
    public $winnerTotal;
    public $rejoinScore;

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

        $this->player   = Player::where('user_id', auth()->id())
                                    ->where('game_id', $gameId)
                                    ->firstOrFail(); 

        $this->paymentsByPlayer = Payments::where('game_id', $gameId)
                                    ->where('player_id', $this->player->id) 
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

    private function allPlayersReported(){
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
        if (!$this->checkForWinner()) {
            $this->checkForRejoin();
            $this->showToLoadingModal = false;          
        }  
        $this->showToLoadingModal = false;      
    }

    private function checkForWinner(){
        $players = Player::where('game_id', $this->selectedGameId)->get();
        $playersOver100 = $players->filter(fn($player) => $player->total_points > 100);

        if ($playersOver100->count() === $players->count() - 1) {
            $winner = $players->first(fn($player) => $player->total_points <= 100);

            if ($winner) {
                $this->showWinnerModal($winner);
                $this->updateGameStatusToFinished($winner);
                return true; 
            }
        }
        return false;  
    }

    private function showWinnerModal($winner){
        $this->winnerName = $winner->nickname;

        $payments = Payments::where('game_id', $this->selectedGameId)
                            ->sum('amount'); 

        $this->winnerTotal = $payments;
        $this->showToWinnerModal = true;  
    }

    private function updateGameStatusToFinished($winner){
        $game = Game::where('id', $this->selectedGameId)->firstOrFail();
        $game->status = 'finished';
        $game->winner = $winner->id;
        $game->save();
    }

    private function checkForRejoin(){
        $player = Player::where('id', $this->selectedPlayerId)
                        ->where('game_id', $this->selectedGameId)
                        ->first();
    
        if ($player && $player->total_points > 100) {
            $maxValidScore = Player::where('game_id', $this->selectedGameId)
                                   ->where('total_points', '<=', 100)
                                   ->max('total_points');
    
            $this->rejoinScore = $maxValidScore;
            $this->selectedPlayerId = $player->id;
            $this->showToRejoinModal = true;
        }
    }

    public function acceptRejoin(){
        $player = Player::findOrFail($this->selectedPlayerId);
        $player->total_points = $this->rejoinScore; 
        $player->save();
        
        $lastTurn = Score::where('player_id', $this->selectedPlayerId)
                          ->where('game_id', $this->selectedGameId)
                          ->max('turn');

        $game = $this->getGameById($this->selectedGameId);

        Score::where('player_id', $this->selectedPlayerId)
            ->where('game_id', $this->selectedGameId)
            ->delete();

        Score::create([
            'player_id' => $this->selectedPlayerId,
            'game_id' => $this->selectedGameId,
            'turn' => $lastTurn, 
            'points' => $this->rejoinScore, 
            'total' => $this->rejoinScore,
            'has_reported' => true,
        ]);
    
        Payments::create([
            'player_id' => $player->id,
            'game_id' => $this->selectedGameId,
            'payment_type' => 'rejoin',
            'amount' => $game->rejoin_price, 
        ]);
    
        $this->showToRejoinModal = false; 
    }

    public function getGameById($gameId){
        return Game::findOrFail($gameId);
    }
    
    
    public function rejectRejoin() {
        $this->removePlayerFromGame($this->selectedPlayerId, $this->selectedGameId);
        $this->showToRejoinModal = false; 
        $game = $this->getGameById($this->selectedGameId);
        return redirect()->route('gamelobby.show', ['gameTypeId' => $game->game_type_id]);
    }

    private function removePlayerFromGame($playerId, $gameId) {
        Player::where('id', $playerId)
              ->where('game_id', $gameId)
              ->delete(); 
    }

    public function confirmLeaveGame() {
        $this->removePlayerFromGame($this->selectedPlayerId, $this->selectedGameId);
        $this->showToLeaveGameModal = false;
    
        $game = $this->getGameById($this->selectedGameId);
        return redirect()->route('gamelobby.show', ['gameTypeId' => $game->game_type_id]);
    }
    
    public function showLeaveGameModal($gameId) {
        $this->selectedGameId = $gameId;
        $this->showToLeaveGameModal = true;
    }
    
    public function hideLeaveGameModal() {
        $this->showToLeaveGameModal = false;
    }

    public function closeWinnerModal(){
        $this->showToWinnerModal = false;   
    }

    public function showExactDealModal($gameId)
    {
        $this->player = Player::where('user_id', auth()->id())
                        ->where('game_id', $gameId)
                        ->firstOrFail(); 
        $this->selectedPlayerId = $this->player->id;
        $this->selectedGameId = $gameId;
        $this->showToExactDealModal = true;
    }

    public function hideExactDealModal()
    {
        $this->showToExactDealModal = false;
    }

    public function confirmExactDeal()
    {
        // Obtener el último turno
        $lastTurn = Score::where('player_id', $this->selectedPlayerId)
                          ->where('game_id', $this->selectedGameId)
                          ->max('turn');
       
        // Si hay un último turno, obtener el puntaje correspondiente
        if ($lastTurn) {
            $lastScore = Score::where('player_id', $this->selectedPlayerId)
                              ->where('game_id', $this->selectedGameId)
                              ->where('turn', $lastTurn)
                              ->first();
    
            // Si se encontró el puntaje, restar 5 puntos
            if ($lastScore) {
                $lastScore->points -= 5;
                $lastScore->total -= 5;
                $lastScore->exact_deal = true; // Marcar como true
                $lastScore->save();
            }
        }
    
        $this->hideExactDealModal();
    }
    public function render(){
        return view('livewire.game-view');
    }
}
