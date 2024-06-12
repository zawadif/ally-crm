<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ModuleNote;
use App\Models\Office;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:unit_list|unit_import|unit_create|unit_edit|unit_view|unit_note-create|unit_note-history', ['only' => ['index','getUnits']]);
        $this->middleware('permission:unit_import', ['only' => ['getUploadUnitCsv']]);
        $this->middleware('permission:unit_create', ['only' => ['create','store']]);
        $this->middleware('permission:unit_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:unit_view', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administrator.units.index');

    }
    public function getUnits(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::with('headOffice')
                ->whereHas('headOffice', function ($query) {
                    $query->where('status', 'active');
                });
            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $units->where(function ($query) use ($searchValue) {
                    $query->where('unit_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('contact_email', 'like', '%' . $searchValue . '%')
                        ->orWhere('unit_postcode', 'like', '%' . $searchValue . '%')
                        ->orWhere('contact_phone_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('contact_landline', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('headOffice', function ($query) use ($searchValue) {
                            $query->where('name', 'like', '%' . $searchValue . '%')
                                ->orWhere('type', 'like', '%' . $searchValue . '%'); // Search the 'type' field of the related 'Office' model
                        });

                });
            }
//            if ($request->has('search') && !empty($request->input('search')['value'])) {
//                $search = $request->input('search')['value'];
//                $units->where(function ($query) use ($search) {
//                    $query->where('unit_name', 'like', '%' . $search . '%')
//                        ->orWhere('contact_email', 'like', '%' . $search . '%')
//                        ->orWhere('unit_postcode', 'like', '%' . $search . '%')
//                        ->orWhere('contact_phone_number', 'like', '%' . $search . '%')
//                        ->orWhere('contact_landline', 'like', '%' . $search . '%')
//                        ->orWhereHas('headOffice', function ($query) use ($search) {
//                            $query->where('name', 'like', '%' . $search . '%');
//                        });
//                });
//            }

            $auth_user=Auth::user();
            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('time', function($row){
                    $time=Carbon::parse($row->unit_added_time)->format('h:i A');
                    return $time;
                })
                ->addColumn('date', function($row){
                    $date=Carbon::parse($row->unit_added_date)->format('jS F Y');
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

                ->addColumn('type',function ($row){
                    $type =Office::where('id',$row->head_office)->first();
                    $name_head_office=ucfirst($type->name);
                    return $name_head_office;
                })
                ->addColumn('notes',function ($row){
                    $detail=$row->unit_notes;
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
                    if ($auth_user->hasPermissionTo('unit_edit')) {
                        $action .= "<a href=\"/units/{$oRow->id}/edit\" class=\"dropdown-item\"> Edit</a>";
                    }
                    if ($auth_user->hasPermissionTo('unit_view')) {
                        $action .= "<a href=\"" . route('units.show', ['unit' => $oRow->id]) . "\" class=\"dropdown-item\"> View </a>";
                    }
                    if ($auth_user->hasPermissionTo('unit_note-create')) {
                        $action .=     "<a href=\"#\" class=\"dropdown-item\"
                                               data-controls-modal=\"#add_unit_note{$oRow->id}\"
                                               data-backdrop=\"static\"
                                               data-keyboard=\"false\" data-toggle=\"modal\"
                                               data-target=\"#add_unit_note{$oRow->id}\">
                                               Add Note
                                </a >";
                    }
                    $action .= "</div>
        </div>
    </div>";
                    $url = route('module-note-store');
                    $csrf = csrf_token();
                    if ($auth_user->hasPermissionTo('unit_note-create')) {
                        $action .= "
        <div id=\"add_unit_note{$oRow->id}\" class=\"modal fade\" tabindex=\"-1\">
            <div class=\"modal-dialog modal-dialog-centered\">
                <div class=\"modal-content\">
                    <div class=\"modal-header bg-primary text-white\">
                        <h5 class=\"modal-title\">Add Unit Note</h5>
                        <button type=\"button\" class=\"close text-white\" data-dismiss=\"modal\">&times;</button>
                    </div>
                    <form action=\"{$url}\" method=\"POST\" class=\"form-horizontal\" id=\"note_form{$oRow->id}\">
                        <input type=\"hidden\" name=\"_token\" value=\"{$csrf}\">
                        <input type=\"hidden\" name=\"module\" value=\"Unit\">
                        <div class=\"modal-body\">
                            <div id=\"note_alert{$oRow->id}\"></div>
                            <div class=\"form-group\">
                                <label class=\"col-form-label\">Details</label>
                                                      <input type=\"hidden\" name=\"module_key\" value=\"{$oRow->id}\">
                                <textarea name=\"details\" id=\"note_details{$oRow->id}\" class=\"form-control\" rows=\"4\" placeholder=\"TYPE HERE ..\" required></textarea>
                            </div>
                        </div>

                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">
                                Close
                            </button>
                            <button type=\"submit\" data-note_key=\"{$oRow->id}\" class=\"btn btn-primary note_form_submit\">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>";
                    }

                    return $action;
                })

                ->rawColumns(['action','date','type','status','notes','time'])
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
        $head_offices = Office::where("status","active")->get();
        return view('administrator.units.create',compact('head_offices'));

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
//dd($request->all());
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'unit_name' => 'required|string|max:255',
                'office_email' => 'required|email|max:255',
                'office_phone' => 'required',
                'office_phoneHome' =>'required',
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
            // Create a new Unit instance and save it to the database
            $unit = new Unit([
                'user_id'=>Auth::id(),
                'head_office' => $request->input('head_office'),
                'unit_name' => $request->input('unit_name'),
                'unit_postcode' => $request->input('office_postcode'),
                'contact_name' => $request->input('contact_name'),
                'contact_phone_number' => $request->input('office_phone'),
                'contact_landline' => $request->input('office_phoneHome'),
                'contact_email' => $request->input('office_email'),
                'website' => $request->input('office_website'),
                'status' => 'active', // Assuming you want to set it as active
                'unit_notes' => $request->input('unit_notes'),
//                'head_office'=>$request->input('head_office')
                'unit_added_date'=>date("jS F Y"),
                'unit_added_time'=>date("h:i A"),
                'lat'=>$latitude,
                'long'=>$longitude,
            ]);

            $unit->save();

            // You can return a response if needed
            return response()->json(['message' => 'Unit created successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
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
        $user=Unit::find($id);
        return view('administrator.units.unit_detail',compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unit=Unit::find($id);
        $head_offices = Office::where("status","active")->get();

        return view('administrator.units.edit',compact('unit','head_offices'));
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
            $validator = Validator::make($request->all(), [
                'unit_name' => 'required|string|max:255',
                'office_email' => 'required|email|max:255',
                'office_phone' => 'required',
                'office_phoneHome' =>'required',
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
            // Find the Unit by ID
            $unit = Unit::find($id);

            if (!$unit) {
                return response()->json(['error' => 'Unit not found.'], 404);
            }

            // Update the Unit instance with the new data
            $unit->head_office = $request->input('head_office');
            $unit->unit_name = $request->input('unit_name');
            $unit->unit_postcode = $request->input('office_postcode');
            $unit->contact_name = $request->input('contact_name');
            $unit->contact_phone_number = $request->input('office_phone');
            $unit->contact_landline = $request->input('office_phoneHome');
            $unit->contact_email = $request->input('office_email');
            $unit->website = $request->input('office_website');
            $unit->unit_notes = $request->input('unit_notes');
            $unit->unit_added_date = date("jS F Y");
            $unit->unit_added_time = date("h:i A");
            $unit->lat = $latitude;
            $unit->long = $longitude;

            $unit->save();

            // You can return a response if needed
            return response()->json(['message' => 'Unit updated successfully'], 200);
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

            $user = Unit::find($id);
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
