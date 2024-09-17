<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Player;
use App\Models\Score;
use App\Models\Payments;

class GameLobby extends Component
{
    public $games;
    public $gameTypeId;
    public $showToCreateGameModal = false;
    public $showToJoinGameModal = false;
    public $gameName;
    public $initialPrice;
    public $rejoinPrice;
    public $selectedGameId;
    public $nickname;
    
    protected $rules = [
        'gameName' => 'required|string|max:255',
        'initialPrice' => 'required|numeric|min:0',
        'rejoinPrice' => 'required|numeric|min:0',
        'nickname' => 'required|string|max:255',
    ];

    public function mount($gameTypeId){
        $this->gameTypeId = $gameTypeId;
        $this->loadRecentGames($this->gameTypeId);
    }

    public function createGame(){
     
        Game::create([
            'game_type_id' => $this->gameTypeId,
            'name' => $this->gameName,
            'initial_price' => $this->initialPrice,
            'rejoin_price' => $this->rejoinPrice,
            'created_by' => auth()->id(),
            'status' => 'created',
        ]);

        $this->reset(['gameName', 'initialPrice', 'rejoinPrice']);
        $this->showToCreateGameModal = false;
       
        $this->loadRecentGames($this->gameTypeId);
    }

    public function joinGame(){
        $game = $this->findGame($this->selectedGameId);

        if (!$game) {
            session()->flash('error', 'Juego no encontrado.');
            return;
        }

        $player = $this->createOrUpdatePlayer($game);

        $this->createPaymentIfNotExists($player, $game);

        $this->updatePlayerScore($player, $game);

        return redirect()->route('game.view', ['gameId' => $game->id]);
    }

    private function findGame($gameId){
        return Game::find($gameId);
    }

    private function createOrUpdatePlayer($game){
        return Player::updateOrCreate(
            ['user_id' => auth()->id(), 'game_id' => $game->id],
            ['nickname' => $this->nickname]
        );
    }

    private function createPaymentIfNotExists($player, $game){
        $paymentExists = Payments::where('player_id', $player->id)
                                ->where('game_id', $game->id)
                                ->where('payment_type', 'initial')
                                ->exists();

        if (!$paymentExists) {
            Payments::create([
                'player_id' => $player->id,
                'game_id' => $game->id,
                'payment_type' => 'initial',
                'amount' => $game->initial_price,
            ]);
        }
    }

    private function updatePlayerScore($player, $game){
        $highestScore = Score::where('game_id', $game->id)->max('points');
        if ($highestScore !== null) {
            $playerScore = Score::where('player_id', $player->id)
                                ->where('game_id', $game->id)
                                ->first();

            if ($playerScore) {
                $playerScore->points = $highestScore;
                $playerScore->save();
            }
        }
    }

    public function resetModal(){
        $this->showToCreateGameModal = false;
        $this->showToJoinGameModal = false;
    }

    public function showCreateGameModal(){
        $this->reset(['gameName', 'initialPrice', 'rejoinPrice']);
        $this->showToCreateGameModal = true;
    }

    public function showJoinGameModal($gameId){
        $this->selectedGameId = $gameId;
        $this->reset('nickname');
        $this->showToJoinGameModal = true;
    }

   
    private function loadRecentGames($gameTypeId){
            $this->games = Game::where('game_type_id', $gameTypeId)
                ->with('creator')
                ->latest()
                ->take(5)
                ->get();
    }

    public function render(){
        return view('livewire.game-lobby');
    }
}
