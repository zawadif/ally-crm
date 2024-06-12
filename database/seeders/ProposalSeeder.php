<?php

namespace Database\Seeders;

use App\Models\Proposal;
use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Proposal::create([
            'teamBy' => 2,
            'ladderId' => 7,
            'categoryId' => 2,
            'seasonId' => 2,
            'weekId' => 1,
            'countryId' => 92,
            'regionId' => 3,
            'status' => 'PENDING',
        ]);
    }
}