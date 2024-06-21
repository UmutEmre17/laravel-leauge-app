<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'current_team'];
    public $timestamps = false;

    public function teams(){
        return $this->hasMany(Team::class);
    }
    
}
