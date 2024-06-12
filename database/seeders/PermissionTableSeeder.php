<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Permission::where('name', '=', 'proposal&challenge')->first() === null) {
            Permission::create(['name' => 'proposal&challenge']);
        }
        if (Permission::where('name', '=', 'matchScore')->first() === null) {
            Permission::create(['name' => 'matchScore']);
        }
        if (Permission::where('name', '=', 'supportRequest')->first() === null) {
            Permission::create(['name' => 'supportRequest']);
        }
        if (Permission::where('name', '=', 'season')->first() === null) {
            Permission::create(['name' => 'season']);
        }
        if (Permission::where('name', '=', 'userProfile')->first() === null) {
            Permission::create(['name' => 'userProfile']);
        }
        if (Permission::where('name', '=', 'all')->first() === null) {
            Permission::create(['name' => 'all']);
        }
    }
}
