<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMatch;

class TeamMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TeamMatch::create([
            'matchableId' => 1,
            'matchableType' => 'PLAYOFF',
            'acceptdTeamId' => 3,
            'teamOneId' => 3,
            'teamTwoId' => 4,
            'ladderId' => 13,
            'categoryId' => 1,
            'seasonId' => 7,
            'wonTeamId' => 3,
            'weekId' => 1,
            'countryId' => 92,
            'regionId' => 1, 
            'cancelBy' => 1,
        ]);
    }
}
