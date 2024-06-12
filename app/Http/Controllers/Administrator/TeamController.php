<?php

namespace App\Http\Controllers\Administrator;

use App\DataTables\TeamsDataTable;
use App\Enums\UserStatusEnum;
use App\Models\LoginDetail;
use Carbon\Carbon;
use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Traits\loggerExceptionTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;


class TeamController extends Controller
{
    use loggerExceptionTrait, FileUploadTrait;

    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('is_admin');

        $this->middleware('permission:user_list|user_create|user_edit|user_enable-disable|user_activity-log', ['only' => ['index','listing']]);
        $this->middleware('permission:user_create', ['only' => ['create','store']]);
        $this->middleware('permission:user_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user_enable-disable', ['only' => ['getUserStatusChange']]);
        $this->middleware('permission:user_activity-log', ['only' => ['activityLogs','userLogs']]);
        $this->middleware('permission:role_assign-role', ['only' => ['assignRoleToUsers']]);
    }

    /**
     * imageDirectory  path of folder where image will be stored on s3 e.g uploads/categories
     *
     * @var string
     */

    /**
     * listing  : list all roles with permissions using yajra/laravel-datatables-oracle: "~9.0"
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {

        $roles = Role::whereNotIn('name', ['super_admin'])->get();
        $users = User::with('roles')->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['admin']);
        })->get();
        return view('administrator.teams.listing', compact('roles','users'));
    }

    /**
     * teams : return json of all team members
     *
     * @return void
     */
    public function teams()
    {
        try {

            if(\request()->ajax()){
                $data =  User::with('roles')->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['Super_admin']);
                })->get();;
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $actionBtn = '<a href="/userView/' . $row->id . '" class="btn btn-info btn-sm rounded-3">View Detail</a>';
                        return $actionBtn;
                    })
                    ->addColumn('name',function ($row){
                      $name=ucfirst($row->fullName);
                      return $name;
                    })
                    ->addColumn('email',function ($row){
                    $email=$row->email;
                    return $email;
                    })
                    ->addColumn('role',function ($row){
//                        $roleName=$row->roles?$row->roles->get
                        $roleNames = $row->roles->pluck('name')->toArray();
                        return $roleNames;
                    })
                    ->addColumn('date',function ($row){
                        $date=Carbon::parse($row->created_at)->format('d M Y');
                        return $date;

                    })
                    ->addColumn('time',function ($row){
                   $time=Carbon::parse($row->created_at)->format('h:i A');
                   return $time;
                    })
                    ->addColumn('status',function ($row){
                        if ($row->status == UserStatusEnum::ACTIVE) {
                            return '<span class="badge badge-pill badge-success">Active</span>';
                        }  else {
                            return '
                          <span class="badge badge-pill badge-danger">Block</span>
                    ';
                        }
                    })

                    ->rawColumns(['action','name','email','role','status','date','time'])
                    ->make(true);
            }

//            return response()->json(['status' => true, 'data' => $teams], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }
    public function viewUser($id){
       $user=User::find($id);
       return view('administrator.teams.userDetail',compact('user'));
    }
    public function loginDetail($id){
        try {
            $teams = LoginDetail::where('user_id',$id)->get();
            return response()->json(['status' => true, 'data' => $teams], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    /**
     * store : store new team member
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $store)
    {
        try {

            DB::beginTransaction();
            $validatedData = $store->validate([
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
//                'phoneNumber' => 'required|string|max:20',
                // Add more validation rules as needed
            ]);
            $user = User::create([
                'fullName' => $store->fullName,
                'email' => $store->email,
//                'phoneNumber' => $store->phoneNumber,
//                'gender' => $store->gender,
                'password' => Hash::make($store->password),

            ]);
            $user->assignRole([$store->role]);
            DB::commit();
            return response()->json(['status' => true, 'data' => 'Team member added successfully!'], JsonResponse::HTTP_OK);
        }catch (ValidationException $e) {
            // If validation fails, return the validation error messages
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        }  catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);

        }
    }
    /**
     * edit : show a team member data for update
     *
     * @param  mixed $id
     * @return void
     */
    public function edit($id)
    {
        try {

            $team = User::with(['roles'])->find($id);
            $selected = $team->roles->pluck('name')->toArray();
//            $roles = Role::pluck('name','name')->all();
            $roles = Role::whereNotIn('name', ['super_admin'])->pluck('name','name')->all();

            return response()->json(['status' => true, 'data' => $team, 'selected' => $selected,'roles'=>$roles], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    /**
     * update : update the given team member
     *
     * @param  mixed $updateRequest
     * @return void
     */
    public function update($id, Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
//                'phoneNumber' => 'required|string|max:20',
                // Add more validation rules as needed
            ]);

            // Fetch the user
            $user = User::find($id);
           $user->syncRoles($request->role);
            // Update user details
            $user->fullName = $validatedData['fullName'];
            $user->email = $validatedData['email'];
//            $user->phoneNumber = $validatedData['phoneNumber'];
            // Update other fields similarly if needed

            // Save changes
            $user->save();

            // Return success response
            return response()->json(['status'=>true,'message' => 'User details updated successfully'], 200);
        } catch (ValidationException $e) {
            // If validation fails, return the validation error messages
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to update user details'], 500);
        }
    }



    /**
     * delete : delete given team member
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $assignedRole = $user->roles;
            if (!empty($assignedRole)) {
                $user->syncRoles($assignedRole->pluck('name')->toArray());
            }
            $user->delete();
            DB::commit();
            toastr()->success('Team member deleted successfully!');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'TeamController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }


    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userId' => 'required|exists:users,id',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors()->toArray()
                ]);
            }
            DB::beginTransaction();
            $user = User::find($request->userId);
            if (!$user) {
                return response()->json(['status' => 422, 'message' => 'User  not found']);
            }
            if ($request->password_confirmation != $request->password) {
                return response()->json(['status' => 422, 'message' => 'invalid! confirm password ']);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
            return response()->json(['status' => 200, 'message' => 'Password changed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }
    public function updateUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if (is_null($user)) {
            return response()->json(['response' => ['status' => false, 'message' => 'Unable to find user with given id!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Ensure the status values correspond to those in the database
        if ($user->status == 'BLOCK') {
            // Update status to BLOCK enum value
            $updated = $user->update([
                'status' => UserStatusEnum::ACTIVE
            ]);
            $message = 'User blocked successfully';
        } elseif ($user->status == 'ACTIVE') {
            // Update status to ACTIVE enum value
            $updated = $user->update([
                'status' => UserStatusEnum::BLOCK
            ]);
            $message = 'User activated successfully';
        } else {
            return response()->json(['error' => 'Invalid action'], 400);
        }

        // Check if the update was successful
        if ($updated) {
            return response()->json(['message' => $message], 200);
        } else {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }
    public function userDelete(Request $request, $id){
        try {
            $user=User::findOrFail($id);
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'Unable to find user with given email!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $user->delete();
            $message = 'User activated successfully';

            return response()->json(['message' => $message], 200);

        }catch (\Exception $exception){

        }
    }
    public function userActivity(Request $request ,$id)
    {

        if(Request()->ajax()){
            $user = User::find($id);
            $data=LoginDetail::where('user_id',$user->id)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } // Assuming your TeamsDataTable has a static make() method

//        return view('users.tables.rankings', compact('dataTable', 'id'));
    }

}
