<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Models\Player;
use App\Models\Score;

class Dashboard extends Component
{
    public $games;
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

    public function mount(){
        $this->games = Game::with('creator')
            ->latest()                       
            ->limit(9)                     
            ->get();
    }

    public function createGame(){
        Game::create([
            'name' => $this->gameName,
            'initial_price' => $this->initialPrice,
            'rejoin_price' => $this->rejoinPrice,
            'created_by' => auth()->id(),
            'status' => 'created',
        ]);
        $this->reset(['gameName', 'initialPrice', 'rejoinPrice']);
        $this->showToCreateGameModal = false;
        $this->games = Game::with('creator')
            ->latest()                       
            ->limit(9)                     
            ->get();
    }

    public function joinGame(){
    $game = Game::find($this->selectedGameId);

    if (!$game) {
        session()->flash('error', 'Juego no encontrado.');
        return;
    }

    $player = Player::updateOrCreate(
        ['user_id' => auth()->id(), 'game_id' => $game->id],
        [
            'nickname' => $this->nickname,
            'game_id' => $game->id,
        ]
    );

    // Opcional: Verificar si la partida ha comenzado y ajustar el puntaje del jugador
    $highestScore = Score::where('game_id', $game->id)->max('points');
    if ($highestScore !== null) {
        $playerScore = Score::where('player_id', $player->id)->where('game_id', $game->id)->first();
        if ($playerScore) {
            $playerScore->points = $highestScore;
            $playerScore->save();
        }
    }
    
    // Redirigir a la vista del juego
    return redirect()->route('game.view', ['gameId' => $game->id]);
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

    public function render(){
        return view('livewire.dashboard');
    }

}
