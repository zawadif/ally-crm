<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Challenge::create([
            'ladderId' => 1,
            'categoryId' =>1,
            'seasonId' => 1,
            'weekId' => 1,
            'countryId' => 93,
            'regionId' => 3,
            'teamBy' => 1,
            'teamTo' => 3,
            'status' => 'PENDING',
        ]);
    }
}
