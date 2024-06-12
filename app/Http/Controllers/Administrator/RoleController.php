<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:role_list|role_create|role_view|role_edit|role_delete|role_assign-role', ['only' => ['index']]);
        $this->middleware('permission:role_create', ['only' => ['create','store']]);
        $this->middleware('permission:Hoffice_role_create', ['only' => ['office_create']]);
        $this->middleware('permission:Hoffice_role_store', ['only' => ['office_store']]);
        $this->middleware('permission:Hoffice_role_update', ['only' => ['update_office']]);
        $this->middleware('permission:role_view', ['only' => ['show']]);
        $this->middleware('permission:role_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $users = User::with('roles')->where('is_admin', '<>', '1')->select('id','fullName')->get();
        $all_roles = Role::where('name', '<>', 'super_admin')->get();

        $roles = Role::orderBy('id','DESC')->paginate(10);
        // print_r($roles);exit();

        // echo '<pre>';print_r($users);echo '</pre>';exit();

        return view('administrator.roles.index',compact('roles', 'all_roles', 'users'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    public function getRoles(Request $request){
        if(\request()->ajax()){
            $all_roles = Role::where('name', '<>', 'super_admin')->get();

            return DataTables::of($all_roles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn='';
                    if (auth()->user()->can('role_edit')) {
                        $actionBtn .= '<a href="/roles/' . $row->id . '/edit" class="btn btn-primary">Edit</a>&nbsp;&nbsp;';
                    }

                    if (auth()->user()->can('role_delete')) {
                        $actionBtn .=
                            '<form method="POST" action="' . route('roles.destroy', $row->id) . '" style="display:inline;">' .
                            csrf_field() .
                            method_field("DELETE") .
                            '<button type="submit" class="btn btn-danger">Delete</button>' .
                            '</form>';
                    }

                    return $actionBtn;
                })
                ->addColumn('name',function ($row){
                    $name=ucfirst($row->name);
                    return $name;
                })

                ->rawColumns(['action','name'])
                ->make(true);
        }
    }
    public function create()
    {
        $permission = Permission::get();
        return view('administrator.roles.create',compact('permission'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
//dd('role store');
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success','Role created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);

        // print_r($role);exit();
        $permission = Permission::get();
        $Hoffice_res = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')->pluck('permissions.name')->all();
        $Hoffice_permissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')->pluck('permissions.name','permissions.id')->all();
        // print_r($Hoffice_permissions);exit();
        $Hoffice_data = array();

        foreach($Hoffice_permissions as $key => $value)
        {
            if(strpos($value, 'Hoffice_', 0) !== false)
            {
                // echo $value;exit();

                // $Hoffice_data[] = $key;
                $Hoffice_data[$key] = $value;
            }

        }

        // print_r($Hoffice_data);exit();

        $Hoffice_status = false;
        foreach($Hoffice_res as $res)
        {
            if(strpos($res, 'Hoffice_', 0) !== false)
            {
                $Hoffice_status = true;
            }

        }
        // $headOffice_per = array();
        // foreach($Hoffice_permissions as $per)
        // {
        //     echo $per->name;exit();
        // }
        //    print_r($Hoffice_permissions[0]->name);exit();
        // DB::table('users')
        //     ->join('contacts', 'users.id', '=', 'contacts.user_id')
        //     ->join('orders', 'users.id', '=', 'orders.user_id')
        //     ->select('users.id', 'contacts.phone', 'orders.price')
        //     ->get();
        // print_r($test);exit();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        // $res = preg_grep('/^Hoffice_\s.*/', $Hoffice_res);
        // // $res = str_contains('Hoffice_', $Hoffice_res);
        // echo $res;exit();
        if($Hoffice_status)
        {
            return view('administrator.roles.office_edit',compact('role','permission','rolePermissions','Hoffice_data'));
        }
        else{
            return view('administrator.roles.edit',compact('role','permission','rolePermissions'));

        }
        // print_r($rolePermissions);exit();
        // echo '<pre'; print_r($permission);echo '</pre>';exit();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        // echo $role->name;exit();

        $role->save();
// print_r($request->input('permission'));exit();
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success','Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Revoke all permissions associated with this role
        $role->permissions()->detach();

        // Delete the role
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
    public function assignRoleToUsers(Request $request)
    {
//        dd('assign role to use');
//        echo $request->input('role');exit();
        // print_r($request->input('role'));exit();
        foreach ($request->input('users') as $user_id) {
            DB::table('model_has_roles')->where('model_id',$user_id)->delete();
            $user = User::find($user_id);
            @$user->assignRole($request->input('role'));
        }
        return redirect()->back()->with('success','Role assigned successfully');
    }



}
