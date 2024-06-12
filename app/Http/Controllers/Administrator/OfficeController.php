<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ModuleNote;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class OfficeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:office_list|office_import|office_create|office_edit|office_view|office_note-history|office_note-create', ['only' => ['index','getOffices']]);
        $this->middleware('permission:office_create', ['only' => ['create','store']]);
        $this->middleware('permission:office_import', ['only' => ['getUploadOfficeCsv']]);
        $this->middleware('permission:office_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:office_view', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administrator.offices.index');

    }
     public function getOffices(Request $request)
    {
        if ($request->ajax()) {
            $applicants = Office::where('status','active')->orderBy('created_at','DESC');
            $auth_user=Auth::user();
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $applicants->where(function ($query) use ($search) {
                    $query
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('postcode', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('contact_number', 'like', '%' . $search . '%')
                        ->orWhere('contact_landline', 'like', '%' . $search . '%')
                        ;
                });
            }
            return DataTables::of($applicants)
                ->addIndexColumn()
                ->addColumn('time', function($row){
                    $time=Carbon::parse($row->office_added_time)->format('h:i A');
                    return $time;
                })
                ->addColumn('date', function($row){
                    $date=Carbon::parse($row->office_added_date)->format('jS F Y');
//                dd($date);
                    return $date;
                })
                ->addColumn('office_name', function($row){
                    $name=$row->name;
                    return $name;
                })
                ->addColumn('status',function ($row){
                    if($row->status == 'active'){
                        $status = '<h5><span class="badge badge-success">Active</span></h5>';
                    }else{
                        $status = '<h5><span class="badge badge-danger">Disable</span></h5>';
                    }
                    return $status;
                })
                ->addColumn('postcode',function ($row){
                    $postcode =$row->postcode;
                    return $postcode;
                })
                ->addColumn('email',function ($row){
                    $email =$row->email;
                    return $email;
                })
                ->addColumn('phone_number',function ($row){
                    $contact_number =$row->contact_number;
                    return $contact_number;
                })
                ->addColumn('contact_landline',function ($row){
                    $contact_landline =$row->contact_landline;
                    return $contact_landline;
                })
                ->addColumn('type',function ($row){
                    $type =$row->type;
                    return $type;
                })
                ->addColumn('notes',function ($row){
                    $detail=$row->office_notes;
                    return $detail;
                })
                ->addColumn('action', function ($oRow) {
                    $auth_user = Auth::user();
                    $action = "<div class=\"btn-group\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-bs-toggle=\"dropdown\">
                <i class=\"bi bi-list\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">";
                    if ($auth_user->hasPermissionTo('office_edit')) {
                        $action .= "<a href=\"/offices/{$oRow->id}/edit\" class=\"dropdown-item\"> Edit</a>";
                    }
                    if ($auth_user->hasPermissionTo('office_view')) {
                        $action .= "<a href=\"" . route('offices.show', ['office' => $oRow->id]) . "\" class=\"dropdown-item\"> View </a>";
                    }
                    if ($auth_user->hasPermissionTo('office_note-create')) {
                        $action .=     "<a href=\"#\" class=\"dropdown-item\"
                                               data-controls-modal=\"#add_office_note{$oRow->id}\"
                                               data-backdrop=\"static\"
                                               data-keyboard=\"false\" data-toggle=\"modal\"
                                               data-target=\"#add_office_note{$oRow->id}\">
                                               Add Note
                                </a >";
                    }
                    $action .= "</div>
        </div>
    </div>";
                    $url = route('module-note-store');
                    $csrf = csrf_token();
                    if ($auth_user->hasPermissionTo('office_note-create')) {
                        $action .= "
        <div class=\"modal fade\" id=\"add_office_note{$oRow->id}\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"addOfficeNoteLabel\" aria-hidden=\"true\">
          <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
            <div class=\"modal-content\">
              <div class=\"modal-header bg-primary text-white\">
                <h5 class=\"modal-title\" id=\"addOfficeNoteLabel\">Add Office Note</h5>
                <button type=\"button\" class=\"close text-white\" data-dismiss=\"modal\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>
              <form action=\"{$url}\" method=\"POST\" class=\"form-horizontal\" id=\"note_form{$oRow->id}\">
                <input type=\"hidden\" name=\"_token\" value=\"{$csrf}\">
                <input type=\"hidden\" name=\"module\" value=\"Office\">
                <div class=\"modal-body\">
                  <div id=\"note_alert{$oRow->id}\"></div>
                  <div class=\"form-group\">
                    <label for=\"note_details{$oRow->id}\" class=\"col-form-label\">Details</label>
                                          <input type=\"hidden\" name=\"module_key\" value=\"{$oRow->id}\">

                    <textarea name=\"details\" id=\"note_details{$oRow->id}\" class=\"form-control\" rows=\"4\" placeholder=\"TYPE HERE ..\" required></textarea>
                  </div>
                </div>
                <div class=\"modal-footer\">
                  <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                  <button type=\"submit\" data-note_key=\"{$oRow->id}\" class=\"btn btn-primary\">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    ";
                    }


                    return $action;
                })

                ->rawColumns(['action','date','office_name','phone_number','contact_landline','type','status','notes','time'])
                ->make(true);

        }


    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.offices.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set('Europe/London');
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'office_name' => 'required|string|max:255',
                'office_email' => 'required|email|max:255',
                'office_phone' => [
                    'required',
                    'string',
                    'max:20', // Adjust max length as needed for international format
                    'regex:/^\+1\s?\(\d{3}\)\s?\d{3}-\d{4}$/',
                ],
                'office_phoneHome' => [
                    'nullable',
                    'string',
                    'max:20', // Adjust max length as needed for international format
                    'regex:/^\+1\s?\(\d{3}\)\s?\d{3}-\d{4}$/',
                ],
                'job_type' => 'required|in:contract,non-contract',
                'office_postcode' => 'required|string|max:20',
                'office_notes' => 'nullable|string',
            ]);

            // If validation fails, return response with errors
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $latitude = 00.000000;
            $longitude = 00.000000;
            $newGeoCode= new Helper();
            $data_arr=$newGeoCode->getCoordinates($request->input('postcode'));
            if ($data_arr) {
                $latitude = $data_arr['latitude'];
                $longitude = $data_arr['longitude'];
            }
//            $office->office_added_date = date("jS F Y");
//            $office->office_added_time = date("h:i A");
            // Create a new Office instance and save it to the database
            $office = new Office([
                'user_id'=>Auth::id(),
                'name' => $request->input('office_name'),
                'email' => $request->input('office_email'),
                'contact_number' => $request->input('office_phone'),
                'contact_landline' => $request->input('office_phoneHome'),
                'type' => $request->input('job_type'),
                'postcode' => $request->input('office_postcode'),
                'office_notes' => $request->input('office_notes'),
                'lat'=>$latitude,
                'long'=>$longitude,
                'website'=>$request->input('office_website'),
                'status'=>'active',
                'office_added_date'=>date("jS F Y"),
                'office_added_time'=>date("h:i A"),
            ]);

            $office->save();

            // You can return a response if needed
            return response()->json(['message' => 'Office created successfully'], 200);

        } catch (\Exception $e) {
            // Log the exception or handle it accordingly
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }



    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=Office::find($id);
        return view('administrator.offices.office_detail',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $office=Office::find($id);
        return view('administrator.offices.edit',compact('office'));
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
        date_default_timezone_set('Europe/London');

        try {
            // Validate the incoming request data

            $validatedData = $request->validate([
                'office_name' => 'required|string|max:255',
                'office_email' => 'required|email|max:255',
                'office_phone' => 'required|unique:offices,contact_number,'. $id,
                'office_phoneHome' =>'required|unique:offices,contact_landline,'. $id,
                'job_type' => 'required',
                'office_postcode' => 'required|unique:offices,postcode,' . $id,
                'office_notes' => 'nullable|string',
            ]);

            // If validation fails, return response with errors


            // Set default latitude and longitude values
            $latitude = 0.000000;
            $longitude = 0.000000;
            $newGeoCode= new Helper();
            $data_arr=$newGeoCode->getCoordinates($request->input('postcode'));
            if ($data_arr) {
                $latitude = $data_arr['latitude'];
                $longitude = $data_arr['longitude'];
            }
            // Find the Office record by ID
            $office = Office::findOrFail($id);

            // Update the Office instance with new data
            $office->update([
                'user_id' => Auth::id(),
                'name' => $request->input('office_name'),
                'email' => $request->input('office_email'),
                'contact_number' => $request->input('office_phone'),
                'contact_landline' => $request->input('office_phoneHome'),
                'type' => $request->input('job_type'),
                'postcode' => $request->input('office_postcode'),
                'office_notes' => $request->input('office_notes'),
                'lat' => $latitude,
                'long' => $longitude,
                'website' => $request->input('office_website'),
                'status' => 'active',
                'office_added_date' => now()->format('jS F Y'),
                'office_added_time' => now()->format('h:i A'),
            ]);

            // Return a response indicating success
            return response()->json(['message' => 'Office updated successfully'], 200);

        }catch (ValidationException $e) {
            // If validation fails, return the validation error messages
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Log the exception or handle it accordingly
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function moduleNotesClients($id){
        if(Request()->ajax()){
            date_default_timezone_set('Europe/London');

            $user = Office::find($id);
            $data=ModuleNote::where('module_noteable_id',$user->id)
                ->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('time', function($row){
                    $time=Carbon::parse($row->module_note_added_time)->format('h:i A');
                    return $time;
                })
                ->addColumn('date', function($row){
                    $date=Carbon::parse($row->module_note_added_date)->format('jS F Y');
                    return $date;
                })
                ->addColumn('status',function ($row){
                    if($row->status == 'active'){
                        $status = '<h5><span class="badge badge-success">Active</span></h5>';
                    }else{
                        $status = '<h5><span class="badge badge-danger">Disable</span></h5>';
                    }
                    return $status;
                })
                ->addColumn('user_name',function ($row){
                    $span = User::find($row->user_id);
                    $name='';
                    if ($span!=null){
                        $name=ucfirst($span->fullName);

                    }
                    return $name;
                })
                ->addColumn('notes',function ($row){
                    $detail=$row->details;
                    return $detail;
                })
                ->rawColumns(['action','date','user_name','status','notes','time'])
                ->make(true);
        }
    }

}
