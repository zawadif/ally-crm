<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ranking;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=3; $i < 10; $i++) { 
            Ranking::create([
                'categoryId' => 1,
                'seasonId' => 7,
                'weekId' =>  1,
                'ladderId' => 10,
                'matchId' => 2,
                'regionId' => 1,
                'teamId' => $i,
                'points' => $i,
                'type' => 'PLAYOFF', 
            ]);
        }
        
    }
}
