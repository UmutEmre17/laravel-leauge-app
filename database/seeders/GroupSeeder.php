<?php

namespace Database\Seeders;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            'CL Group' => [
                'Manchester City' =>5,
                'Real Madrid' => 5,
                'Dortmund' =>4,
                'Fenerbahce' =>4,

            ]
            ];

        foreach ($groups as $group=>$teams){
            $groupModel = Group::updateOrCreate(['name' => $group]);

            foreach ($teams as $team => $strength) {
                $groupModel->teams()->updateOrCreate(['name' => $team], ['strength' => $strength]);

            }
        }
    }
}
