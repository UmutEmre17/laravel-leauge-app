<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function getMatchesPlayedAttribute(){
        return $this->win + $this->draw + $this->lose;
    }  
    
    public function getGoalsDifferenceAttribute(){
        return $this->goals_for - $this->goals_against;
    }   
    
    public function getPtsAttribute(){
        return ($this->win *3) + $this->draw;
    }    


    
}
