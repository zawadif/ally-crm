<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeedController extends Controller
{
//    public function __construct()
//    {
////        $this->middleware('auth');
////        $this->middleware('is_admin');
//    }
    public function storePermissions()
    {
        $modules_with_permissions = config('crm-permissions');
//        dd($modules_with_permissions);

        foreach ($modules_with_permissions as $module_with_permissions) {
            foreach ($module_with_permissions as $permission)
                Permission::create(['name' => $permission]);
        }

        $this->assignPermissionsToAdmin();

        return 'done!';
    }

    public function assignPermissionsToAdmin()
    {
        $admin_user = User::where('email', 'developers@ibstec.com')->first();

        $role = Role::create(['name' => 'super_admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $admin_user->assignRole([$role->id]);
    }
}
