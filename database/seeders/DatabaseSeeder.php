<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Database\Seeders\PrivacyPolicy;
use Illuminate\Support\Facades\App;
use Database\Seeders\TermConditionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(RegionTableSeeder::class);
        if (App::environment(['local']) ||  App::environment(['staging']) || App::environment(['acceptance'])) {
            $this->call(UserFirebaseDeleteSeeder::class);
        }
        $this->call(AdminSeeder::class);
        $this->call(AllLadderSeeder::class);
        if (App::environment(['local']) ||  App::environment(['staging']) || App::environment(['acceptance'])) {
            $this->call(RegionalAdminSeeder::class);
        }
        if (App::environment(['local']) ||  App::environment(['staging']) || App::environment(['acceptance'])) {
            $this->call(PrivacyPolicy::class);
            $this->call(TermConditionSeeder::class);
        }

        $this->call(ConfigurationSeeder::class);

        // Testing
        // $this->call(RegionalAdminSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(TeamSeeder::class);
        // $this->call(PurchaseSeeder::class);
        // $this->call(ChallengeSeeder::class);
        // $this->call(RankSeeder::class);
        // $this->call(TeamMatchSeeder::class);
    }
}
