<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'initial_price', 'rejoin_price','created_by', 'status'];

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
}
