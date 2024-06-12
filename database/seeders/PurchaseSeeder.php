<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Purchase::create([
            'amountedUserId' =>2,
            'teamId' => 1,
            'ladderId' => 1,
            'seasonId' => 1,
            'price' =>  13.16,
        ]);
    }
}
