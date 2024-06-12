<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\RoleTypeEnum;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Role::where('name', '=', 'admin')->first() === null) {
            $role = Role::create(['name' => 'admin','type'=>RoleTypeEnum::SYSTEM_CREATED]);
            $role->givePermissionTo('all');
        }
        if (Role::where('name', '=', 'user')->first() === null) {
            Role::create(['name' => 'user','type'=>RoleTypeEnum::SYSTEM_CREATED]);
        }
    }
}
