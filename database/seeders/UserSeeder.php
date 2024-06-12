<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 100; $i++) { 
            $password = Hash::make('TechSwivel#001');
            $user = User::create([
                'fullName' => 'abdul'.$i,
                'email' => 'abdul'.$i.'@techswivel.com',
                'password' => $password,
            ]);
            UserDetail::create([
                'userId' => $user->id,
                'gender' => 'MALE',
                'phoneNumber' => '033111112'.$i,
                'countryId' => 92,
                'dob' => strtotime("now"),
                'completeAddress' => 'Model Town',
                'city' => 'Lahore',
                'postalCode' => '54000',
                'state' => 'punjab',
                'emergencyContactNumber' => '03333333333',
                'emergencyContactName' => 'aftab',
                'emergencyContactRelation' => 'father', 
                'bio' => 'Hello world',
            ]);
            $user->assignRole('user');
        }
        
    }
}
