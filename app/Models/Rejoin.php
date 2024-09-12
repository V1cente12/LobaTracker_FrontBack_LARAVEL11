<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rejoin extends Model
{
    use HasFactory;

    protected $fillable = ['player_id', 'game_id', 'initial_score', 'rejoin_at'];

    //relationships
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
