<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'game_id', 'nickname', 'total_points', 'has_reported'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
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
}
