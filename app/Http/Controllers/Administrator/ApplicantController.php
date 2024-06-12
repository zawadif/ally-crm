<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\ClientWelcomeEmail;
use App\Models\ApplicantNote;
use App\Models\Client;
use App\Models\CvNote;
use App\Models\History;
use App\Models\ModuleNote;
use App\Models\Sale;
use App\Models\Specialist_job_titles;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Horsefly\Applicant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class ApplicantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:applicant_list|applicant_import|applicant_create|applicant_edit|applicant_view|applicant_history|applicant_note-create|applicant_note-history', ['only' => ['index','getApplicants']]);
        $this->middleware('permission:applicant_import', ['only' => ['getUploadApplicantCsv']]);
        $this->middleware('permission:applicant_create', ['only' => ['create','store']]);
        $this->middleware('permission:idle_applicants', ['only' => ['idle']]);
        $this->middleware('permission:applicant_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:applicant_view', ['only' => ['show']]);
        $this->middleware('permission:applicant_history', ['only' => ['getApplicantHistory','getApplicantFullHistory']]);
        $this->middleware('permission:resource_No-Nursing-Home_list|resource_No-Nursing-Home_revert-no-nursing-home', ['only' => ['getNurseHomeApplicants','getNurseHomeApplicantsAjax']]);
        $this->middleware('permission:resource_Non-Interested-Applicants', ['only' => ['getNonInterestedApplicants','getNonInterestAppAjax']]);
        $this->middleware('permission:resource_Potential-Callback_revert-callback|resource_No-Nursing-Home_revert-no-nursing-home', ['only' => ['revertApplicants']]);
        $this->middleware('permission:applicant_export', ['only' => ['export_csv','export','exportNurseHomeApplicants','exportNonInterestedLastApplicants','export_block_applicants']]);

    }
    public function index(){
        return view('administrator.applicants.index');

    }
    public function getApplicants(Request $request)
    {
        if ($request->ajax()) {
            $applicants = Client::select([
                'app_name',
                'app_email',
                'app_phone',
                'app_phoneHome',
                'app_job_title',
                'app_job_category',
                'app_source',
                'app_status',
                'app_postcode',
                'app_lat',
                'app_long',
                'applicant_update_cv',
                'applicant_added_time',
                'created_at',
                'applicant_notes','id','app_job_title_prof'
            ])->where('app_status','active')->orderBy('created_at','DESC');
            $auth_user=Auth::user();
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $searchValue = $request->input('search')['value'];
                $applicants->where(function ($query) use ($searchValue) {
                    $query->where('app_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('app_email', 'like', '%' . $searchValue . '%')
                        ->orWhere('app_phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('app_phoneHome', 'like', '%' . $searchValue . '%')
                        ->orWhere('app_postcode', 'like', '%' . $searchValue . '%')
                        ->orWhere('app_job_category', 'like', '%' . $searchValue . '%');
                });
            }
            return DataTables::of($applicants)

                ->addColumn('action', function ($applicant) use ($auth_user) {
                    // Logic to generate action buttons based on user permissions
                    if ($auth_user->hasAnyPermission(['applicant_edit','applicant_view','applicant_history','applicant_note-create','applicant_note-history'])) {
                        $action = '<div class="btn-group">
                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                            <i class="bi bi-list"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                        // Check if the user has permission to edit
                        if ($auth_user->hasPermissionTo('applicant_edit')) {
                            $action .= '<li><a class="dropdown-item" href="/clients/' . $applicant->id . '/edit">Edit</a></li>';
                        }

                        // Check if the user has permission to disable client
//                        if ($auth_user->hasPermissionTo('applicant_disable')) {
                            $action .= '<li><a class="dropdown-item" href="javascript:void(0);" onclick="confirmDelete(' . $applicant->id . ')">Disable Client</a></li>';
//                        }

                        // Check if the user has permission to view history
                        if ($auth_user->hasPermissionTo('applicant_history')) {
                            $action .= '<li><a class="dropdown-item" href="' . route('applicantHistory', $applicant->id) . '">History</a></li>';
                        }
                        $action .= '<li><a class="dropdown-item" href="#" onclick="openNotesHistoryModal(' . $applicant->id . ')" data-toggle="modal" data-target="#notesHistoryModal">Notes History</a></li>';

                        // Add more actions as needed

                        $action .= '</ul></div>';

                        return $action;
                    }
                })

                ->addColumn('applicant_added_date',function ($row){
                    $time=Carbon::parse($row->created_at)->format('d M Y');
//dd($row->applicant_added_date);
                    return $time;
                })
                ->addColumn('applicant_added_time',function ($row){
                    $date=Carbon::parse($row->applicant_added_time)->format('h:i A');

                    return $date;
                })
//                ->addColumn('app_email',function ($row){
//                    return $row->app_email;
//                })
                ->editColumn('applicant_job_title', function ($applicant) {
                    // $job_title_desc = ($close_sales->job_title_prof!='')?$close_sales->job_title.' ('.$close_sales->job_title_prof.')':$close_sales->job_title;
                    // return $job_title_desc;
                    // return $applicant->applicant_job_title.' test';
                    if($applicant->app_job_title == 'nurse specialist' || $applicant->app_job_title == 'nonnurse specialist')
                    {
                        $selected_prof_data = Specialist_job_titles::select("name")->where("id", $applicant->app_job_title_prof)->first();
                        if($selected_prof_data)
                        {
                            $spec_job_title = ($applicant->app_job_title_prof!='')?$applicant->app_job_title.' ('.$selected_prof_data->name.')':$applicant->app_job_title;
                            return $spec_job_title;

                        }
                        else
                        {
                            return $applicant->app_job_title;
                        }
                    }
                    else
                    {
                        return $applicant->app_job_title;
                    }

                })

                ->addColumn('download',function ($row){
                    return
                        '<a href="' . route('downloadApplicantCv', $row->id) . '">
                       <span><i class="fa fa-file-download"></i></span>
                    </a>';
//                    $homeNumber='<span><i class="fa fa-file-download"></i></span>';
//
//                    return $homeNumber;
                })

                ->addColumn('upload', function ($row) {
                    return '<a href="#" onclick="uploadCv(' . $row->id . ')" class="import_cv" data-controls-modal="#import_applicant_cv" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#import_applicant_cv"><span><i class="fa fa-file-download"></i></span>&nbsp;</a>';
                })


                ->addColumn('upload_cv',function ($row){
                    return
                        '<a href="' . route('downloadUpdatedApplicantCv', $row->id) . '">
                       <span><i class="fa fa-file-upload"></i></span>
                    </a>';
                })
                ->addColumn('applicant_notes', function($applicants){
//dd($applicants);
                    $app_new_note = ModuleNote::where(['module_noteable_id' =>$applicants->id,
                        'module_noteable_type' =>'App\Models\Client'])
                        ->select('module_notes.details')
                        ->orderBy('module_notes.id', 'DESC')
                        ->first();
                    $app_notes_final='';
                    if($app_new_note){
                        $app_notes_final = $app_new_note->details;

                    }
                    else{
                        $app_notes_final = $applicants->applicant_notes;
                    }

                    $status_value = 'open';
                    $postcode = '';
                    if ($applicants->paid_status == 'close') {
                        $status_value = 'paid';
                    } else {
                        foreach ($applicants->cv_notes as $key => $value) {
                            if ($value->status == 'active') {
                                $status_value = 'sent';
                                break;
                            } elseif ($value->status == 'disable') {
                                $status_value = 'reject';
                            }
                        }
//                                $status_value = 'reject';

                    }

                    if($applicants->is_blocked == 0 && $status_value == 'open' || $status_value == 'reject')
                    {

                        $content = '';
                        // if ($status_value == 'open' || $status_value == 'reject'){

                        $content .= '<a href="#" class="reject_history" data-applicant="'.$applicants->id.'"
                                 data-controls-modal="#clear_cv'.$applicants->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#clear_cv' . $applicants->id . '">"'.$app_notes_final.'"</a>';
                        $content .= '<div id="clear_cv' . $applicants->id . '" class="modal fade" tabindex="-1">';
                        $content .= '<div class="modal-dialog modal-lg">';
                        $content .= '<div class="modal-content">';
                        $content .= '<div class="modal-header">';
                        $content .= '<h5 class="modal-title">Notes</h5>';
                        $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                        $content .= '</div>';
                        $content .= '<form action="' . route('block_or_casual_notes') . '" method="POST" id="app_notes_form' . $applicants->id . '" class="form-horizontal">';
                        $content .= csrf_field();
                        $content .= '<div class="modal-body">';
                        $content .='<div id="app_notes_alert' . $applicants->id . '"></div>';
                        $content .= '<div id="sent_cv_alert' . $applicants->id . '"></div>';
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Details</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicants->id . '">';
                        $content .= '<input type="hidden" name="applicant_page' . $applicants->id . '" value="applicants">';
                        $content .= '<textarea name="details" id="sent_cv_details' . $applicants->id .'" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<select name="reject_reason" class="form-control crm_select_reason" id="reason' . $applicants->id .'">';
                        $content .= '<option value="0" >Select</option>';
                        $content .= '<option value="1">Casual Reason</option>';
                        $content .= '<option value="2">Block client Reason</option>';
                        $content .= '<option value="3">Temporary Not Interested client Reason</option>';
//                        $content .= '<option value="4">No Response</option>';
                        $content .= '</select>';
                        $content .= '</div>';
                        $content .= '</div>';

                        $content .= '</div>';
                        $content .= '<div class="modal-footer">';

                        $content .= '<button type="button" class="btn btn-dark legitRipple sent_cv_submit" data-dismiss="modal" style="border-radius: 0 17px 0 17px;">Close</button>';

                        $content .= '<button type="submit" data-note_key="' . $applicants->id . '" value="cv_sent_save" class="btn bg-teal  sent_cv_submit app_notes_form_submit greenButton" style="border-radius: 17px 0 17px 0;">Save</button>';

                        $content .= '</div>';
                        $content .= '</form>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '</div>';
                        // } else {
                        // $content .= $applicant->applicant_notes;
                        // }

                        //return $app_notes_final;
                        return $content;
                    }else
                    {
                        return $app_notes_final;
                    }

                })
                ->rawColumns(['action','applicant_added_time','applicant_added_date','download','upload_cv','upload','applicant_notes','app_email','applicant_job_title']) // To render HTML in the 'action' column
                ->make(true);
        }


//        return abort(404);
    }
    public function create(){
        $applicant_source = array(
            'Total Jobs' => 'Total Jobs',
            'Read' => 'Read',
            'Niche' => 'Niche',
            'CV Library' => 'CV Library',
            'Social Media' => 'Social Media',
            'Referral' => 'Referral',
            'Other Source' => 'Other Source');
        return view('administrator.applicants.create', compact('applicant_source'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validate([
                'app_name' => 'required|string|max:255',
                'app_email' => 'required|email',
                'app_postcode' => 'required',
                'app_phone' => 'unique:clients',
                'app_phoneHome' => 'unique:clients',
                // Add validation rules for other fields
            ]);
            date_default_timezone_set('Europe/London');

            $latitude = 00.000000;
            $longitude = 00.000000;
            $newGeoCode= new Helper();
            $data_arr=$newGeoCode->getCoordinates($request->input('app_postcode'));
            if ($data_arr) {
                $latitude = $data_arr['latitude'];
                $longitude = $data_arr['longitude'];
            }

            if ($request->hasFile('applicant_cv')) {
//                dd('file');
                $validator = $request->validate([
                    // 'applicant_email' => 'email|unique:applicants',
                    'applicant_cv' => 'required|file|mimes:doc,docx,csv,pdf|max:10240',

//                    'applicant_cv' => 'required|file|mimes:doc,docx,cvs|max:10240', // Adjust max file size as needed
                ]);

                $filenameWithExt = $request->file('applicant_cv')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('applicant_cv')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $path = $request->file('applicant_cv')->move('uploads/', $fileNameToStore);
            } else {
                $path = 'old_image';
            }
            // Create a new Client instance and set its attributes
            $client = new Client();
            $client->user_id = Auth::id();
            $client->app_name = $request->app_name;
            $client->app_email = $request->app_email;
            $client->app_phoneHome = $request->app_phoneHome;
            $client->app_phone = $request->app_phone;
            $client->app_job_category=$request->app_job_category;
            $client->app_job_title=$request->job_title;
            $client->app_postcode=$request->app_postcode;
            $client->applicant_added_time=Carbon::now()->format('H:i:s');
            $client->applicant_added_date=Carbon::now()->format('d M Y');
            $client->app_source=$request->app_source;
            $client->app_status='active';
            $client->app_lat = $latitude;
            $client->app_long = $longitude;
            $client->applicant_cv=$path;
            $client->applicant_update_cv='active';
            $client->applicant_notes=$request->applicant_notes;
            $client->app_job_title_prof=$request->job_title_special; //TODO SPECIALLIST TABLE ADDED TO ID THIS && AND LAT AND LONG STORE
            // Set other attributes similarly

            // Save the client data

            $client->save();
            Mail::to($request->app_email)->send(new ClientWelcomeEmail($client)); // Pass the client data to the Mailable class

           DB::commit();

            return response()->json(['success' => 'Form submitted successfully']);
        }catch (ValidationException $e) {
            // If validation fails, return the validation error messages
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        }  catch (\Exception $e) {

          DB::rollBack();
            return response()->json(['error' => 'Something went wrong. Please try again.'],400);
        }
    }


    public function edit($id){
        try {
            $applicant=Client::find($id);
          $applicant_source = array(
            'Total Jobs' => 'Total Jobs',
            'Read' => 'Read',
            'Niche' => 'Niche',
            'CV Library' => 'CV Library',
            'Social Media' => 'Social Media',
            'Referral' => 'Referral',
            'Other Source' => 'Other Source');
            return view('administrator.applicants.edit',compact('applicant','applicant_source'));
        }catch (\Exception $exception){
         return redirect('/clients');
        }
    }
    public function update(Request $request ,$id){
        try {
            date_default_timezone_set('Europe/London');
            $auth_user = Auth::user()->id;
            $applicant = Client::find($id);

            if ($request->hasFile('applicant_cv')) {
                $validatedData = $request->validate([
                    // 'applicant_email' => 'email|unique:applicants',
//                    'applicant_cv' => 'required|file|mimes:doc,docx,csv,pdf|max:10240',
                    'applicant_cv' => 'required|file|max:10240',

//                    'applicant_cv' => 'required|file|mimes:doc,docx,cvs|max:10240', // Adjust max file size as needed
                ]);

                $filenameWithExt = $request->file('applicant_cv')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('applicant_cv')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $path = $request->file('applicant_cv')->move('uploads/', $fileNameToStore);
            } else {
                if (!is_null($applicant->applicant_cv)) {
                    $path = $applicant->applicant_cv;
                } else {
                    $path = null; // or any default value you prefer
                }
            }
            $validatedData = $request->validate([
                'app_email' => 'email|unique:clients,app_email,' . $id,
//            'applicant_job_title' => 'required',
                'app_postcode' => 'required|unique:clients,app_postcode,' . $id,
                'app_phone' => 'unique:clients,app_phone,' . $id,
                'app_homePhone' => 'unique:clients,app_homePhone,' . $id,
            ]);

//        dd($request->all());


            $latitude = 00.000000;
            $longitude = 00.000000;
            $newGeoCode= new Helper();
            $data_arr=$newGeoCode->getCoordinates($request->input('app_postcode'));
            if ($data_arr) {
                $latitude = $data_arr['latitude'];
                $longitude = $data_arr['longitude'];
            }
            $new_job_title = $request->get('job_title');
            if($new_job_title === 'nurse specialist' || $new_job_title === 'nonnurse specialist'){
                $app_job_title_prof= $request->get('job_title_special');
            }
            else
            {
                $app_job_title_prof=  $applicant->app_job_title_prof? $applicant->app_job_title_prof:null;
            }
            $applicant->user_id=$auth_user;
            $applicant->app_name=$request->app_name?$request->app_name:$applicant->app_name;
            $applicant->app_email=$request->app_email?$request->app_email:$applicant->app_email;
            $applicant->app_phone=$request->app_phone?$request->app_phone:$applicant->app_phone;
            $applicant->app_phoneHome=$request->app_phoneHome?$request->app_phoneHome:$applicant->app_phoneHome;
            $applicant->app_source=$request->app_source?$request->app_source:$applicant->app_source;
            $applicant->app_postcode=$request->app_postcode?$request->app_postcode:$applicant->app_postcode;
            $applicant->app_postcode=$request->app_postcode?$request->app_postcode:$applicant->app_postcode;
            $applicant->app_lat=$latitude;
            $applicant->app_long=$longitude;
            $applicant->applicant_added_date = date("jS F Y");
            $applicant->applicant_added_time = date("h:i A");
            $applicant->app_job_title_prof=$app_job_title_prof;
            $applicant->applicant_cv = $path;
            $applicant->app_job_category=$request->app_job_category?$request->app_job_category:$applicant->app_job_category;
            $applicant->app_job_title=$request->job_title?$request->job_title:$applicant->job_title;
            $applicant->applicant_notes = $request->applicant_notes?$request->applicant_notes:$applicant->applicant_notes;
            $applicant->update();

             if ($applicant){
                 ModuleNote::create([
                     'user_id'=>Auth::id(),
                     'module_noteable_id'=>$applicant->id,
                     'module_noteable_type' =>'App\Models\Client',
                     'details' =>$request->applicant_notes?$request->applicant_notes:$applicant->applicant_notes,
                     'module_note_added_time'=>date("h:i A"),
                     'module_note_added_date'=>date("jS F Y"),
                     'status'=>'active'
                 ]);
             }

            return redirect('clients')->with('updateSuccessMsg', 'Client has been updated');

        }
        catch (ValidationException $e) {
//            dd($e);
            // If validation fails, return the validation error messages
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        }catch (\Exception $e){
            //dd($e->getMessage());
//            return redirect()->ba->with('error', 'Client has been updated');

            return response()->json(['errors' => $e->getMessage()], 422);

        }

         }
    public function history( $id){
        date_default_timezone_set('Europe/London');

        $notes=ModuleNote::where('module_noteable_id',$id)
            ->where('module_noteable_type','App\Models\Client')
//            ->whereRaw('user_id != 1')
           ->with('user')->orderBy('id','desc')->get();
        return view('administrator.applicants.history', compact('notes'));
    }





    public function getUploadApplicantCsv(Request $request){
        date_default_timezone_set('Europe/London');
        if ($request->file('applicant_csv') != null ){
            $file = $request->file('applicant_csv');

            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                // Check file size
                if($fileSize <= $maxFileSize){
                    // File upload location
                    $location = 'uploads';

                    // Start transaction
//                    DB::beginTransaction();

                    try {
                        // Upload file
                        $file->move($location,$filename);

                        // Import CSV to Database
                        $filepath = public_path($location."/".$filename);
                        $file = fopen($filepath,"r");

                        $importData_arr = array();
                        $i = 0;

                        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                            $num = count($filedata );

                            // Skip first row
                            if($i == 0){
                                $i++;
                                continue;
                            }
                            for ($c=0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata [$c];
                            }
                            $i++;
                        }
                        fclose($file);
//dd($importData_arr);
                        foreach($importData_arr as $importData){
                            $phoneNumber = $importData[4];
                            if (strlen($phoneNumber) == 10) { // Assuming phone number is 10 digits long
                                $phoneNumber = '0' . $phoneNumber;
                            }
                            // Check if record with the same phoneNumber or email exists
                            $existingRecord = Client::where('app_phone', $phoneNumber)
                                ->orWhere('app_email', $importData[5])
                                ->first();
                            $postcode = $importData[3];
                            $first_three_digits = substr($postcode, 0, 3);
                            if (!$existingRecord) {
                                // Record does not exist, proceed with insertion
                                // Import data to database

                                $newGeoCode= new Helper();
                                $latitude = 00.000000;
                                $longitude = 00.000000;

                                try {
                                    // Attempt to retrieve coordinates
                                    $data_arr = $newGeoCode->getCoordinates($first_three_digits);

                                    // If data is available, update latitude and longitude
                                    if ($data_arr) {
                                        $latitude = $data_arr['latitude'];
                                        $longitude = $data_arr['longitude'];
                                    }
                                } catch (\Exception $e) {
                                    // Handle the exception, if needed
                                    // You can log the error or display a message to the user
                                    dd( $e->getMessage());
                                }
                                if ($importData[2]=='non-nurses'){
                                    $job='non-nurses';
                                }elseif ($importData[2]=='nurses'){
                                    $job='nurses';

                                }else{
                                    $job='nurses';
                                }
                                $name=$importData[0];
                                $explode = explode(' ', $name);
                                $applicant = new Client();
//                                $applicant->applicant_added_date = $importData[0];
                                $applicant->app_name =$explode[0];
                                $applicant->app_job_title = $importData[1];
                                $applicant->app_phone = $phoneNumber;
                                $applicant->app_phoneHome ='';
                                $applicant->app_email = $importData[6];
                                $applicant->app_job_category = $job;
                                $applicant->app_source = $importData[6];
                                $applicant->app_status = 'active'; // Assuming default status is 'active'
                                $applicant->app_postcode = $postcode;
                                $applicant->app_lat = $latitude;
                                $applicant->app_long = $longitude;
                                $applicant->applicant_notes = '';
                                $applicant->user_id = Auth::user()->id;
                                $applicant->applicant_added_date =date("Y-m-d");
                                $applicant->applicant_added_time = date("h:i A");
                                $applicant->created_at = Carbon::now();
                                $applicant->updated_at = Carbon::now();
                                $applicant->save();
                            } else {
                                // Record already exists, skip insertion
                                continue;
                            }
                        }

                        // Commit transaction
//                        DB::commit();

                        toastr()->success('Import Successful.');
                    } catch (\Exception $e) {
//                        dd($e->getMessage());
                        // Rollback transaction if an error occurs
//                        DB::rollBack();

                        toastr()->error('Import failed. Please try again.');
                    }

                    return redirect()->back();
                } else {
//                    dd('invlaid large file');

                    toastr()->error('File too large. File must be less than 2MB.');
                }
            } else {
//                dd('invlaid else');
                toastr()->error('Invalid File Extension.');
            }
        }
        return redirect('clients');
    }

    public function store_block_or_casual_notes(Request $request)
    {
        date_default_timezone_set('Europe/London');

        //  echo 'casual or blocked notes';exit();
        $applicant_id = $request->Input('applicant_hidden_id');
        //$sale_id = $request->Input('applicant_sale_id');
        $applicant_notes = $request->Input('details');

        $notes_reason = $request->Input('reject_reason');
        // echo $applicant_id.' sale_id: '.$sale_id .' applicant_notes:  '.$applicant_notes.' notes reason: '.$notes_reason; exit();

        $applicant_page = $request->Input('applicant_page'.$applicant_id);



        if($notes_reason =='2')
        {
            Client::where('id', $applicant_id)
                ->update(['no_response'=>'0','is_blocked' => '1','temp_not_interested'=>'0','applicant_notes' => $applicant_notes]);
            ModuleNote::create([
                'user_id'=>Auth::id(),
                'module_noteable_id'=>$applicant_id,
                'module_noteable_type' =>'App\Models\Client',
                'details' => $applicant_notes,
                'module_note_added_time'=>date("h:i A"),
                'module_note_added_date'=>date("jS F Y"),
                'status'=>'active'
            ]);

        }
        else if($notes_reason =='1')
        {
            Client::where('id', $applicant_id)
                ->update(['applicant_notes' => $applicant_notes]);
            ModuleNote::create([
                'user_id'=>Auth::id(),
                'module_noteable_id'=>$applicant_id,
                'module_noteable_type' =>'App\Models\Client',
                'details' => $applicant_notes,
                'module_note_added_time'=>date("h:i A"),
                'module_note_added_date'=>date("jS F Y"),
                'status'=>'active'
            ]);

        }
        else if($notes_reason=='3')
        {
            Client::where('id', $applicant_id)
                ->update(['no_response'=>'0','temp_not_interested' => '1','is_blocked' => '0','applicant_notes' => $applicant_notes]);

            ModuleNote::create([
                'user_id'=>Auth::id(),
                'module_noteable_id'=>$applicant_id,
                'module_noteable_type' =>'App\Models\Client',
                'details' => $applicant_notes,
                'module_note_added_time'=>date("h:i A"),
                'module_note_added_date'=>date("jS F Y"),
                'status'=>'active'
            ]);
        }
//        else if($notes_reason=='4')
//        {
//            Client::where('id', $applicant_id)
//                ->update(['no_response'=>'1','temp_not_interested' => '0','is_blocked' => '0','applicant_notes' => $applicant_notes]);
//        }



//dd('7days and 24 days',$applicant_page);
        // echo $applicant_id.' notes: '.$applicant_notes.' reason : '.$notes_reason.' date: '.$end_date;exit();
        // return redirect()->route('getlast2MonthsApp');[+]
        if($applicant_page == 'applicants')
        {
            return redirect('clients');
        } else if($applicant_page == '2_months_applicants')
        {

            // $interval = 60;
            // return view('administrator.resource.last_2_months_applicant_added', compact('interval'));
            // return redirect('last2months');
            return redirect()->route('last2months');
        }
        else if($applicant_page == '7_days_applicants')
        {
            return redirect()->route('last7days');

        }
        else if($applicant_page == '21_days_applicants')
        {
            return redirect()->route('last21days');

        }
        else if($applicant_page == '15_km_clients_nurses')
        {
//            dd('saas');
            $sale_id=$request->applicant_sale_id;
            return redirect('clients-within-15-km/'.$sale_id);
            // return redirect()->route('applicants-within-15-km/'.$sale_id);
        }


    }
    public function destroy($id)
    {
        $applicant = Client::find($id);
        $status = $applicant->app_status;
        if ($status == 'active') {
            if (DB::table('clients')->where('id', $id)->update(['app_status' => 'disable','is_blocked'=>1])) {
//                return redirect('applicants')->with('ApplicantDeleteSuccessMsg', 'Applicant has been disabled Successfully');
                return response()->json(['status' => true, 'data' => 'Applicant disable successfully!'], JsonResponse::HTTP_OK);

            } else {
                return response()->json(['status' => false, 'data' => 'WHOOPS! Something Went Wrong!'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

//                return redirect('applicants')->with('ApplicantDeleteErrMsg', 'WHOOPS! Something Went Wrong!!');
            }

        } elseif ($status == 'disable') {
            if (DB::table('clients')->where('id', $id)->update(['app_status' => 'active'])) {
                return response()->json(['status' => true, 'data' => 'Applicant has been enabled Successfully!'], JsonResponse::HTTP_OK);

//                return redirect('applicants')->with('ApplicantDeleteSuccessMsg', 'Applicant has been enabled Successfully');
            } else {
                return response()->json(['status' => false, 'data' => 'WHOOPS! Something Went Wrong!'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

//                return redirect('applicants')->with('ApplicantDeleteErrMsg', 'WHOOPS! Something Went Wrong!!');
            }
        }
    }
    public function applicantDetail($id){
        $user=Client::find($id);

        return view('administrator.applicants.applicant_detail',compact('user'));
    }
    public function moduleNotesClients($id)
    {
        if (request()->ajax()) {
            try {
                date_default_timezone_set('Europe/London');
                $user = Client::findOrFail($id);

                $moduleNotes = ModuleNote::where('module_noteable_id', $user->id)
                    ->where('module_noteable_type', 'App\Models\Client')
                    ->orderBy('id', 'DESC')
                    ->get();

                // Fetch applicant notes
                $applicantNotes = ApplicantNote::whereIn('moved_tab_to', ['callback', 'revert_callback'])
                    ->where('client_id', $user->id)
                    ->orderBy('id', 'DESC')
                    ->get();

                $mergedNotes = collect([]);

                if ($moduleNotes->isNotEmpty()) {
                    $mergedNotes = $mergedNotes->merge($moduleNotes);
                }

                if ($applicantNotes->isNotEmpty()) {
                    $mergedNotes = $mergedNotes->merge($applicantNotes);
                }

                $mergedNotes = $mergedNotes->unique('id')->sortByDesc('id');

                return datatables()->of($mergedNotes)
                    ->addIndexColumn()
                    ->addColumn('time', function ($row) {
                        // Format time if applicable
                        return isset($row->applicant_added_time)
                            ? Carbon::parse($row->applicant_added_time)->format('h:i A')
                            : (isset($row->module_note_added_time)
                                ? Carbon::parse($row->module_note_added_time)->format('h:i A')
                                : '');
                    })
                    ->addColumn('date', function ($row) {
                        // Format date if applicable
                        return isset($row->applicant_added_date)
                            ? Carbon::parse($row->applicant_added_date)->format('jS F Y')
                            : (isset($row->module_note_added_date)
                                ? Carbon::parse($row->module_note_added_date)->format('jS F Y')
                                : '');
                    })
                    ->addColumn('status', function ($row) {
                        // Set status badge
                        return $row->status == 'active'
                            ? '<h5><span class="badge badge-success">Active</span></h5>'
                            : '<h5><span class="badge badge-danger">Disable</span></h5>';
                    })
                    ->addColumn('user_name', function ($row) {
                        // Get user name
                        $user = User::find($row->user_id);
                        return $user ? ucfirst($user->fullName) : '';
                    })
                    ->addColumn('notes', function ($row) {
                        // Add logic to display notes based on note type (client, module, applicant)
                        return $row instanceof Client ? $row->applicant_notes : $row->details;
                    })
                    ->addColumn('moved_tab_to', function ($row) {
                        // Add logic to determine the moved_tab_to value
                        return $row instanceof ApplicantNote ? $row->moved_tab_to : 'module_note';
                    })
                    ->rawColumns(['time', 'date', 'status', 'user_name', 'notes', 'moved_tab_to'])
                    ->make(true);
            } catch (\Exception $e) {
                // Log the error or return an error response
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function cvQualityNotesClients($id){
        if(Request()->ajax()){
            date_default_timezone_set('Europe/London');

            $user = Client::find($id);

            $applicants_in_crm = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
                ->join('sales', 'sales.id', '=', 'crm_notes.sale_id')
                ->join('offices', 'offices.id', '=', 'head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->join('histories', function($join) {
                    $join->on('crm_notes.client_id', '=', 'histories.client_id');
                    $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
                })
                ->select("clients.*", "clients.id as app_id", "crm_notes.*", "crm_notes.id as crm_notes_id", "sales.*", "sales.id as sale_id", "sales.postcode as sale_postcode", "sales.job_title as sale_job_title", "sales.job_category as sales_job_category", "sales.status as sale_status", "histories.history_added_date", "histories.sub_stage","name", "unit_name")
                ->where(array('clients.id' => $id, 'histories.status' => 'active'))
                ->whereIn('crm_notes.id', function($query){
                    $query->select(DB::raw('MAX(id) FROM crm_notes WHERE sale_id=sales.id and clients.id=client_id'));
                })
                ->get();

//            $applicant_crm_notes = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
//                ->select("crm_notes.*", "crm_notes.sale_id as sale_id", "clients.id as app_id")
//                ->where(['crm_notes.client_id' => $id])
//                ->orderBy("crm_notes.created_at", "desc")
//                ->get();
//
//
//            return DataTables::of($applicant_crm_notes)
//                ->addIndexColumn()
//                ->addColumn('time', function($row){
//                    $time=Carbon::parse($row->module_note_added_time)->format('h:i A');
//                    return $time;
//                })
//                ->addColumn('date', function($row){
//                    $date=Carbon::parse($row->module_note_added_date)->format('jS F Y');
//                    return $date;
//                })
//                ->addColumn('status',function ($row){
//                    if($row->status == 'active'){
//                        $status = '<h5><span class="badge badge-success">Active</span></h5>';
//                    }else{
//                        $status = '<h5><span class="badge badge-danger">Disable</span></h5>';
//                    }
//                    return $status;
//                })
//                ->addColumn('user_name',function ($row){
//                    $span = User::find($row->user_id);
//                    $name='';
//                    if ($span!=null){
//                        $name=ucfirst($span->fullName);
//
//                    }
//                    return $name;
//                })
//                ->addColumn('notes',function ($row){
//                  $detail=$row->details;
//                    return $detail;
//                })
//                ->rawColumns(['action','date','user_name','status','notes','time'])
//                ->make(true);

            $applicant_crm_notes = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
                ->join('sales', 'sales.id', '=', 'crm_notes.sale_id') // Join the sales table
                ->join('offices', 'offices.id', '=', 'head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->join('histories', function($join) {
                    $join->on('crm_notes.client_id', '=', 'histories.client_id');
                    $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
                })
                ->select(
                    'crm_notes.crm_added_date as date',
                    'crm_notes.sale_id',
                    'sales.job_title as title', // Use the correct column name: 'sale.job_title'
                    'sales.postcode',
                    'sales.job_title',
                    'sales.job_category',
                    'offices.name as head_office',
                    'units.unit_name as unit',
                    'histories.sub_stage as stage',
                    'crm_notes.details as note'
                )
                ->where('crm_notes.client_id', $id)
                ->orderBy("crm_notes.crm_added_date", "desc");

//dd($applicant_crm_notes->count());

            // Return the data in the DataTables format
            return DataTables::of($applicant_crm_notes)
                ->make(true);
        }
    }


    public function getDownloadApplicantCv($cv_id)
    {
        $url = url()->previous();

        $applicant = Client::select("applicant_cv")->where('id', $cv_id)->first();

        if ($applicant->applicant_cv != null) {
            $file = $applicant->applicant_cv;

            // Check if the file exists before attempting to download
            if (file_exists(public_path($file))) {
                $headers = [
                    'Content-Type' => $this->getMimeType($file),
                    'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
                ];

                return Response::download(public_path($file), null, $headers);
            } else {
                toastr()->error('CV file not found!');
                return redirect($url)->with('error', 'CV file not found!');
            }
        } else {
            toastr()->error('CV for this applicant is not uploaded yet!');

            return redirect($url)->with('error', 'CV for this applicant is not uploaded yet!');
        }
    }
    public function getUpdatedDownloadApplicantCv($cv_id)
    {
        $url = url()->previous();

        $applicant = Client::select("applicant_update_cv")->where('id', $cv_id)->first();

        if ($applicant->applicant_update_cv != null) {
            $file = $applicant->applicant_update_cv;

            // Check if the file exists before attempting to download
            if (file_exists(public_path($file))) {
                $headers = [
                    'Content-Type' => $this->getMimeType($file),
                    'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
                ];

                return Response::download(public_path($file), null, $headers);
            } else {
                return redirect($url)->with('error', 'CV file not found!');
            }
        } else {
            return redirect($url)->with('error', 'CV for this applicant is not uploaded yet!');
        }
    }

    private function getMimeType($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'pdf':
                return 'application/pdf';
            case 'doc':
            case 'docx':
                return 'application/msword';
            // Add more cases for other file types if needed
            default:
                return 'application/octet-stream';
        }
    }



    public function UploadApplicantCV(Request $request)
    {
        date_default_timezone_set('Europe/London');
        $auth_user = Auth::user()->id;

        $applicant_id = $request->input('applicant_id');

        if ($request->hasFile('applicant_cv')) {
            $filenameWithExt = $request->file('applicant_cv')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('applicant_cv')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('applicant_cv')->move('uploads/', $fileNameToStore);
        } else {
            $path = 'old_image';
        }

        $result = DB::table('clients')->where('id', $applicant_id)->update(['applicant_update_cv' => $path]);

        if ($result) {
            // Return success response as JSON
            return response()->json(['success' => true, 'message' => 'Applicant CV Updated Successfully']);
        } else {
            // Return error response as JSON
            return response()->json(['success' => false, 'message' => 'Applicant CV Could Not Be Updated!']);
        }
    }

    public function getApplicantCvSendToQuality($applicant_cv_id)
    {
        try {
            $audit_data['action'] = "Send CV";
            date_default_timezone_set('Europe/London');
            $audit_data['applicant'] = $applicant = request()->applicant_hidden_id;
            $audit_data['sale'] = $sale = request()->sale_hidden_id;
            $applicant_title_prof = Client::find($audit_data['applicant']);
            $sale_title_prof = Sale::find($sale);
            if ($applicant_title_prof->app_job_title_prof == $sale_title_prof->job_title_prof) {


                // echo $sale_title_prof->job_title_prof;exit();

                $detail_note = request()->details;

                $sale_details = Sale::find($sale);
                if ($sale_details) {
//                DB::beginTransaction();
                    $sent_cv_count = CvNote::where(['sale_id' => $sale, 'status' => 'active'])->count();
                    if ($sent_cv_count < $sale_details->send_cv_limit) {

                        $applicants_rejected = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                            ->where('clients.app_status', 'active');
                        $applicants_rejected = $applicants_rejected->where('is_in_crm_reject', 1)//yes
                        ->orWhere('is_in_crm_request_reject', 1) //yes
                        ->orWhere('is_crm_interview_attended', 0)//no
                        ->orWhere('is_in_crm_start_date_hold', 1) //yes
                        ->orWhere('is_in_crm_dispute', 'yes')
                            ->orWhere([['is_CV_reject', 1], ["quality_notes.moved_tab_to", "rejected"]])
                            ->get();

                        $rejectedApp = 0;
                        foreach ($applicants_rejected as $app) {
                            if ($app->id == $applicant_cv_id) {
                                $rejectedApp = 1;
                            }
                        }
                        // echo 'limit'.$rejectedApp;exit();
//                    Carbon::now()->format("Y-m-d");
//                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                        Client::where('id', $applicant_cv_id)->update(['is_cv_in_quality' => 1]);//yes
                        $user = Auth::user();
                        $current_user_id = $user->id;
                        $cv_note = new CvNote();
                        $cv_note->sale_id = $sale;
                        $cv_note->user_id = $current_user_id;
                        $cv_note->client_id = $applicant;
                        $cv_note->status = 'active';
                        $audit_data['detail_note'] = $cv_note->details = $detail_note;
                        $audit_data['added_date'] = $cv_note->send_added_date = Carbon::now()->format("Y-m-d");
                        $audit_data['added_time'] = $cv_note->send_added_time = Carbon::now()->format("H:i:s");
                        $cv_note->save();
                        $last_inserted_note = $cv_note->id;
                        if ($last_inserted_note > 0) {
                            $history = new History();
                            $history->client_id = $applicant;
                            $history->user_id = $current_user_id;
                            $history->sale_id = $sale;
                            $history->status = 'active';
                            $history->stage = 'quality';
                            $history->sub_stage = 'quality_cvs';
                            $history->history_added_date = Carbon::now()->format("Y-m-d");
                            $history->history_added_time = Carbon::now()->format("H:i:s");
                            $history->save();
                            $last_inserted_history = $history->id;
                            if ($last_inserted_history > 0) {

                                if ($rejectedApp == 1) {
//                                    DB::commit();
                                    return Redirect::back()->with('qualityApplicantMsg', 'Applicant has been sent to quality');
                                } else


                                    return Redirect::back()->with('qualityApplicantErr', 'Applicant Cant be Sent');
                            }
                        } else {
                            if ($rejectedApp == 1) {

//                                DB::commit();

                                return Redirect::back()->with('qualityApplicantMsg', 'Applicant has been sent to quality');
                            }else{
//                                dd('error1');
                                return Redirect::back()->with('qualityApplicantErr', 'Applicant Cant be Sent');
                            }
                        }
                    } else {
                        return Redirect::back()->with('notFoundCv', 'WHOOPS! You cannot perform this action. Send CV Limit for this Sale has reached maximum.');
                    }
                } else {
                    return Redirect::back()->with('notFoundCv', 'Sale not found.');
                }
            } else {

                return Redirect::back()->with('error', 'Specialist Title is mismatched!');
            }
        }catch (\Exception $e) {
//            dd($e->getMessage());
            // If an exception occurs, rollback the transaction
//            DB::rollback();

            // Log or handle the exception as needed
            return Redirect::back()->with('error', 'An error occurred while processing your request.');
        }
    }
    public function getNurseHomeApplicant()
    {
        date_default_timezone_set('Europe/London');
        $audit_data['action'] = "No Nursing Home";
        $details = request()->details;
        $applicant_id = request()->applicant_hidden_id;
        $user = Auth::user();
//        dd($user);
        ApplicantNote::where('client_id', $applicant_id)
            ->whereIn('moved_tab_to', ['no_nursing_home','revert_no_nursing_home'])
            ->update(['status' => 'disable']);
//        dd('df');
        $applicant_note = new ApplicantNote();
        $applicant_note->user_id = $user->id;
        $applicant_note->client_id = $applicant_id;
         $applicant_note->added_date =  Carbon::now()->format("Y-m-d");
        $applicant_note->added_time = Carbon::now()->format("H:i:s");
        $audit_data['details'] = $applicant_note->details = $details;
        $applicant_note->moved_tab_to = "no_nursing_home";
        $applicant_note->status = "active";
        $applicant_note->save();
//        dd('sad');
        $last_inserted_note = $applicant_note->id;
//        dd($last_inserted_note);
        if ($last_inserted_note > 0) {
            Client::where(['id' => $applicant_id])->update(['is_in_nurse_home' => 1]);
            /*** activity log
             * $action_observer = new ActionObserver();
             * $action_observer->action($audit_data, 'Resource');
             */
            toastr()->success('Client has been Moved');
            return Redirect::back()->with('revertNurseHomeApplicantMsg', 'Applicant has been Moved');
        }
        return redirect()->back();
    }

    public function revertTempInterest($id) {
        try {
            $client = Client::find($id);

            if ($client) {
                $client->update([
                    'temp_not_interested' => 0
                ]);

                // Return success response
                return response()->json(['success' => true]);
            } else {
                // Return false response if client not found
                return response()->json(['success' => false, 'message' => 'Client not found.']);
            }
        } catch (\Exception $exception) {
            // Return false response if an exception occurs
            return response()->json(['success' => false, 'message' => 'Error occurred while reverting temp interest.']);
        }
    }


    public function getApplicantHistory($applicant_history_id)
    {
        $auth_user = Auth::user()->id;

        //APPLICANT SEND AGAINST THIS JOB IN QUALITY FROM SEARCH RESULTS
        /*$cv_send_in_quality_notes = Cv_note::where(array('applicant_id' => $applicant_history_id, 'status' => 'active'))->get();
        $applicant_sale = array();
        foreach($cv_send_in_quality_notes as $sales){
            $applicant_sale[] = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->select('sales.*', 'offices.office_name','units.unit_name')
                ->where(['sales.status' => 'active','sales.id' => $sales->sale_id])->first();
        }*/
        //        echo '<pre>';print_r($applicant_sale);exit;

        $applicants_in_crm = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
            ->join('sales', 'sales.id', '=', 'crm_notes.sale_id')
            ->join('offices', 'offices.id', '=', 'head_office')
            ->join('units', 'units.id', '=', 'sales.head_office_unit')
            ->join('histories', function($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })
            ->select("clients.*", "clients.id as app_id", "crm_notes.*", "crm_notes.id as crm_notes_id", "sales.*", "sales.id as sale_id", "sales.postcode as sale_postcode", "sales.job_title as sale_job_title", "sales.job_category as sales_job_category", "sales.status as sale_status", "histories.history_added_date", "histories.sub_stage","name", "unit_name")
            ->where(array('clients.id' => $applicant_history_id, 'histories.status' => 'active'))
            ->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE sale_id=sales.id and clients.id=client_id'));
            })
            ->get();

        $applicant_crm_notes = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
            ->select("crm_notes.*", "crm_notes.sale_id as sale_id", "clients.id as app_id")
            ->where(['crm_notes.client_id' => $applicant_history_id])
            ->orderBy("crm_notes.created_at", "desc")
            ->get();

        $applicant = Client::with('callback_notes','no_nursing_home_notes')->find($applicant_history_id);
//        $applicant = Client::find($applicant_history_id);

        $history_stages = config('constants.history_stages');
//        print_r($history_stages);exit();
        $crm_stages = config('constants.crm_stages');
//dd($applicant_crm_notes);

        // ./APPLICANT SEND AGAINST THIS JOB IN QUALITY FROM SEARCH RESULTS
        return view('administrator.applicants.applicant_detail',compact('applicants_in_crm', 'applicant_crm_notes', 'history_stages', 'crm_stages', 'applicant'));
    }



}
