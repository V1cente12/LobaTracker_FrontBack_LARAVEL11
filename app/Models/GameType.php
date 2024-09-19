<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;
    
    //fields
    protected $fillable = ['name', 'image_path'];
    
    //relationship
    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
