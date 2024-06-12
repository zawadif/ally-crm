<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=2; $i < 30; $i++) { 
            Team::create([
                'ladderId' => 13,
                'firstMemberId' => $i,
                'SecondMemberId' => ++$i,
            ]);
        }

        
    }
}
