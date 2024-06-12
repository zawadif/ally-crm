<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AllLadder;

class AllLadderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $singleMenLevel = ['3.0','3.5','4.0','4.5+'];
        $singleWomenLevel = ['2.5','3.0','3.5','4.0'];
        foreach ($singleMenLevel as $singleManLevel) {
            AllLadder::create([
                'name' => 'Men',
                'level' => $singleManLevel,
                'categoryId' =>  1,
            ]);
        }
        foreach ($singleWomenLevel as $singleWomanLevel) {
            AllLadder::create([
                'name' => 'Women',
                'level' => $singleWomanLevel,
                'categoryId' =>  1,
            ]);
        }
        $doubleMenLevel = ['3.5','4.0+'];
        $doubleWomenLevel = ['2.5','3.0','3.5'];
        foreach ($doubleMenLevel as $doubleManLevel) {
            AllLadder::create([
                'name' => 'Men',
                'level' => $doubleManLevel,
                'categoryId' =>  2,
            ]);
        }
        foreach ($doubleWomenLevel as $doubleWomanLevel) {
            AllLadder::create([
                'name' => 'Women',
                'level' => $doubleWomanLevel,
                'categoryId' =>  2,
            ]);
        }
        $MixedDoubleLevels = ['6.0','7.0/8.0'];
        foreach ($MixedDoubleLevels as $MixedDoubleLevel) {
            AllLadder::create([
                'name' => 'Mixed Double',
                'level' => $MixedDoubleLevel,
                'categoryId' =>  3,
            ]);
        }
    }
}
