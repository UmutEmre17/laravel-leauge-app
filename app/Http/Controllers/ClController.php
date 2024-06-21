<?php

namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Http\Request;

class ClController extends Controller
{
    public function index(){
        $matches = session('matches', []);
        $group = Group::with(['teams' => function ($query) {
            $query->orderBy('pts', 'desc');
        }])->find(1);

        if ($group->teams->min('matches_played') >= 3) {
            $this->calculateChampionshipProbability($group);
        }

        return view('welcome', compact('group', 'matches'));
    }
    

    public function playMatches()
    {
        $groups = Group::with('teams')->get();
        $matches = session('matches', []);
    
        foreach ($groups as $group) {
            $teams = $group->teams->all();
            $teamCount = count($teams);
            session()->put('matches', $matches);
            $teamIndices = range(0, $teamCount - 1);
            shuffle($teamIndices);
        
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $team1Index = $teamIndices[$i * 2];
                $team2Index = $teamIndices[$i * 2 + 1];
                $team1 = $teams[$team1Index];
                $team2 = $teams[$team2Index];
                $this->playMatch($team1, $team2, $matches);
            }
        }
        
        session()->put('matches', $matches);
        return redirect()->route('index');
    }

   

    public function playAllRemainingGames(Group $group)
    {
        $matches = session('matches', []); // Set a default value for $matches
    
        if (!$group) {
            // Handle the case when $group is null
            return redirect()->route('index')->with('error', 'Group not found.');
        }
    
        $teams = $group->teams()->get();
    
        if ($teams) {
            foreach ($teams as $team1) {
                foreach ($teams as $team2) {
                    if ($team1->id != $team2->id && $team1->matches_played < 6 && $team2->matches_played < 6) {
                        $this->playMatch($team1, $team2, $matches);
                    }
                }
            }
        }
    
        session()->put('matches', $matches);
    
        return redirect()->route('index')->with('success', 'All remaining games played successfully.');
    }
    


    private function playMatch(Team $team1, Team $team2, &$matches)
    {
        if ($team1->matches_played >= 6 || $team2->matches_played >= 6) {
            
            return; 
        }
        
        $team1Goals = rand(0, $team1->strength + 2);
        $team2Goals = rand(0, $team2->strength);

        if ($team1Goals > $team2Goals) {
            $this->updateTeamStats($team1, $team2, $team1Goals, $team2Goals);
            $matches[] = "$team1->name : $team1Goals - $team2Goals : $team2->name";
        } elseif ($team1Goals < $team2Goals) {
            $this->updateTeamStats($team2, $team1, $team2Goals, $team1Goals);
            $matches[] = "$team1->name : $team1Goals - $team2Goals : $team2->name";
        } else {
            $this->updateDrawStats($team1, $team2, $team1Goals, $team2Goals);
            $matches[] = "$team1->name : $team1Goals - $team2Goals : $team2->name";
        }
        $team1->matches_played++ ;
        $team2->matches_played++ ;

        
    }

    private function updateTeamStats(Team $winner, Team $loser, $winnerGoals, $loserGoals)
    {
        $winner->win += 1;
        $winner->goals_for += $winnerGoals;
        $winner->goals_against += $loserGoals;
        $winner->pts = ($winner->win * 3) + $winner->draw; 
        $winner->save();
    
        $loser->lose += 1;
        $loser->goals_for += $loserGoals;
        $loser->goals_against += $winnerGoals;
        $loser->pts = ($loser->win * 3) + $loser->draw; 
        $loser->save();
    }
    
    private function updateDrawStats(Team $team1, Team $team2, $team1Goals, $team2Goals)
    {
        $team1->draw += 1;
        $team1->goals_for += $team1Goals;
        $team1->goals_against += $team2Goals;
        $team1->pts = ($team1->win * 3) + $team1->draw; 
        $team1->save();
    
        $team2->draw += 1;
        $team2->goals_for += $team2Goals;
        $team2->goals_against += $team1Goals;
        $team2->pts = ($team2->win * 3) + $team2->draw; 
        $team2->save();
    }


    private function calculateChampionshipProbability($group)
    {
        $teams = $group->teams;
        $lastWeek = $teams->min('matches_played') == 6;
    
        
        $maxPoints = $teams->max('pts');
        $secondMaxPoints = $teams->where('pts', '<', $maxPoints)->max('pts');
        $insurmountableLeadThreshold = 3 * (6 - $teams->min('matches_played'));
    
        if ($maxPoints > $secondMaxPoints + $insurmountableLeadThreshold) {
            // A team has guranteed the championship
            foreach ($teams as $team) {
                $team->cmp_prob = $team->pts == $maxPoints ? 100 : 0;
                $team->save();
            }
        } elseif ($lastWeek) {
            // Last week 
            foreach ($teams as $team) {
                $team->cmp_prob = $team->pts == $maxPoints ? 100 : 0;
                $team->save();
            }
        } else {
            
            foreach ($teams as $team) {
                if ($team->pts + $insurmountableLeadThreshold < $maxPoints) {
                    $team->cmp_prob = 0;
                } else {
                    $team->cmp_prob = null; 
                }
            }
    
            
            $totalPoints = $teams->sum('pts');
            foreach ($teams as $team) {
                if ($team->cmp_prob !== 0) {
                    $team->cmp_prob = round($totalPoints > 0 ? ($team->pts / $totalPoints) * 100 : 0);
                }
                $team->save();
            }
        }
    }
    
           
        
            
   
    

    public function reset()
    {
        session()->forget('matches');
        Artisan::call('migrate:refresh --seed');

        return redirect()->route('index');
    }
}
    

   
        


