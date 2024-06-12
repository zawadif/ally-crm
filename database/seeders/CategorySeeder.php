<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Single', 'Double', 'Mixed Double'];
        $image = ['img/ladders/maleSingle.png', 'img/ladders/maleDouble.png', 'img/ladders/mixDouble.png'];
        foreach ($categories as $index => $category) {
            if (Category::where('name', '=', $category)->first() === null) {
                Category::create([
                    'name' => $category,
                    'imageUrl' => $image[$index],
                ]);
            }
        }
    }
}
