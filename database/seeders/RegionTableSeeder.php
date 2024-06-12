<?php
namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class RegionTableSeeder extends Seeder
{
    public function run()
    {


        if (App::environment(['local']) ||  App::environment(['staging']) || App::environment(['acceptance'])) {

            Region::create([
                'countryId' => 167,
                'name' => 'Charlotte',
                'code' => 'NC',
            ]);
            Region::create([
                'countryId' => 167,
                'name' => 'New York',
                'code' => 'NC',
            ]);
            Region::create([
                'countryId' => 167,
                'name' => 'Washington, D.C.',
                'code' => 'NC',
            ]);
        }
    }
}
