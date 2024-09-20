<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['game_type_id','name', 'initial_price', 'rejoin_price','created_by', 'status', 'winner'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function gameLogs()
    {
        return $this->hasMany(GameLog::class);
    }
    public function payments()
    {
        return $this->hasMany(Payments::class);
    }
    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }
    public function winnerPlayer()
    {
        return $this->belongsTo(Player::class, 'winner');
    }
}
