<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Region;
use App\Models\UserDetail;
use App\Models\UserRegion;
use App\Enums\RoleTypeEnum;
use App\Enums\RegionAssignEnum;
use App\Services\FirebaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RegionalAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ((env('APP_ENV') == 'local') || (env('APP_ENV') == 'staging') || (env('APP_ENV') == 'acceptance')) {
            $password = Hash::make('TechSwivel#001');
            $firebase = new FirebaseService;
            $firebaseUser = $firebase->createUserAccount(['email' =>'muhammad.usama@techswivel.com', 'password' => $password]);
            if (!$firebaseUser->status) {
                dd($firebaseUser->message);
            }
            $userUUID = $firebaseUser->data->uid;

            $user = User::create([
                'fullName' => 'QA1',
                'email' => 'muhammad.usama@techswivel.com',
                'password' => $password,
                'uid' => $userUUID
            ]);
            UserDetail::create([
                'userId' => $user->id,
                'gender' => 'MALE',
                'regionId' => 1,
                'phoneNumber' => '0306213281',
                'country' => "Pakistan",
            ]);

            $user->assignRole('user');
            $role = Role::create([
                'name' => 'regional admin',
                'guard_name' => 'web',
                'type' => RoleTypeEnum::SYSTEM_CREATED,
            ]);
            $user->assignRole($role->name);
            $user->givePermissionTo('all');
            $regions = Region::all();
            foreach ($regions as $region) {
                UserRegion::create([
                    'userId' => $user->id,
                    'assignBy' => RegionAssignEnum::ADMIN_ASSIGN,
                    'regionId' => $region->id,
                ]);
            }
        }
    }
}
