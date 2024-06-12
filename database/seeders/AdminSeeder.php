<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserRegion;
use App\Enums\RegionAssignEnum;
use App\Services\FirebaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $password="TechSwivel#001";

        $user = User::create([
            'fullName' => 'QA',
            'email' => 'developers@ibstec.com',
            'password' => $password,
            'phoneNumber'=>'923085092631'

        ]);

//        $user->assignRole('super_admin');
    }
}
