<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CvNote;
use App\Models\ModuleNote;
use App\Models\Office;
use App\Models\Sale;
use App\Models\Sales_notes;
use App\Models\Specialist_job_titles;
use App\Models\Unit;
use App\Models\User;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*** Sales - Open */
        $this->middleware('permission:sale_list|sale_import|sale_create|sale_edit|sale_view|sale_close|sale_manager-detail|sale_history|sale_notes|sale_note-create|sale_note-history', ['only' => ['index','getSales']]);
        $this->middleware('permission:sale_import', ['only' => ['getUploadSaleCsv']]);
        $this->middleware('permission:sale_create', ['only' => ['create','store']]);
        $this->middleware('permission:sale_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:sale_view', ['only' => ['show']]);
        $this->middleware('permission:sale_close', ['only' => ['getCloseSale']]);
        $this->middleware('permission:sale_on-hold', ['only' => ['onHoldSale','unHoldSale']]);
        $this->middleware('permission:sale_history', ['only' => ['getSaleHistory','getSaleFullHistory']]);
        $this->middleware('permission:sale_notes', ['only' => ['getAllOpenedSalesNotes']]);
        /*** Sales - Close */
        $this->middleware('permission:sale_closed-sales-list|sale_open|sale_closed-sale-notes', ['only' => ['getAllClosedSales']]);
        $this->middleware('permission:sale_on-hold', ['only' => ['getOnHoldSales']]);
        $this->middleware('permission:sale_on-hold', ['only' => ['getAllOnHoldSales']]);
        $this->middleware('permission:sale_open', ['only' => ['getOpenSale']]);
        $this->middleware('permission:sale_closed-sale-notes', ['only' => ['getAllClosedSalesNotes']]);
        /*** Sales - PSL */
        $this->middleware('permission:sale_psl-offices-list|sale_psl-office-details|sale_psl-office-units', ['only' => ['getAllPslClientSale']]);
        $this->middleware('permission:sale_psl-office-units', ['only' => ['getAllPslUnitDetails']]);
        /*** Sales - NON PSL */
        $this->middleware('permission:sale_non-psl-offices-list|sale_non-psl-office-details|sale_non-psl-office-units', ['only' => ['getAllNonPslClientSale']]);
        $this->middleware('permission:sale_non-psl-office-units', ['only' => ['getAllNonPslUnitDetails']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administrator.sales.index');

    }
    public function getSales(Request $request)
    {
        if ($request->ajax()) {
            $auth_user = Auth::user();
            date_default_timezone_set('Europe/London');

            $result = Sale::with(['office', 'unit', 'user'])
                ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName', 'units.contact_email',
                    'units.unit_name', 'units.contact_phone_number')
                ->leftJoin('offices', 'offices.id', '=', 'sales.head_office')
                ->leftJoin('units', 'units.id', '=', 'sales.head_office_unit')
                ->leftJoin('users', 'users.id', '=', 'sales.user_id')
                ->where('sales.status', 'active')->orderBy('sales.created_at', 'desc');
//                ->where('sales.is_on_hold', 0);

//            if ($office) {
//                $result->where('sales.head_office', '=', $office);
//            }
//
//            if ($job_category) {
//                $result->where('sales.job_category', '=', $job_category);
//            }
//
//            if ($specialist_title == "nurse specialist" || $specialist_title == "nonnurse specialist") {
//                $result->where('sales.job_title', '=', $specialist_title);
//            }

//            if ($cv_sent_option) {
//                $result->where(function ($query) use ($cv_sent_option) {
//                    if ($cv_sent_option == 'max') {
//                        $query->whereRaw('(SELECT COUNT(*) FROM cv_notes WHERE cv_notes.sale_id = sales.id AND cv_notes.status = "active") = sales.send_cv_limit');
//                    } elseif ($cv_sent_option == 'not_max') {
//                        $query->whereRaw('(SELECT COUNT(*) FROM cv_notes WHERE cv_notes.sale_id = sales.id AND cv_notes.status = "active") > 0')
//                            ->whereRaw('(SELECT COUNT(*) FROM cv_notes WHERE cv_notes.sale_id = sales.id AND cv_notes.status = "active") <> sales.send_cv_limit');
//                    } elseif ($cv_sent_option == 'zero') {
//                        $query->whereRaw('(SELECT COUNT(*) FROM cv_notes WHERE cv_notes.sale_id = sales.id AND cv_notes.status = "active") = 0');
//                    }
//                });
//            }

            $result->addSelect([
                'no_of_sent_cv' => CvNote::selectRaw('COUNT(*)')
                    ->whereColumn('cv_notes.sale_id', 'sales.id')
                    ->where('cv_notes.status', 'active')
                    ->getQuery()
            ]);

            $sales = $result->get();
//            dd($sales);

// $sales now contains the records based on the specified conditions

            $auth_user=Auth::user();
            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('sale_added_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('jS F Y');
                })
                ->addColumn('no_of_sent_cv', function ($row) {
                    $status = $row->no_of_sent_cv == $row->send_cv_limit ?
                        '<span class="badge w-100 badge-danger" style="font-size:90%">Limit Reached</span>' :
                        "<span class='badge w-100 badge-success' style='font-size:90%'>" . ((int)$row->send_cv_limit - (int)$row->no_of_sent_cv) . " Cv's limit remaining</span>";
                    return $status;
                })
                ->addColumn('agent_by', function ($row) {
                    $sale_note=Sales_notes::where('sale_id',$row->id)->first();
                    $user=User::where('id',$sale_note->user_id)->first();
                    $user_name=ucfirst($user->fullName);
                    return $user_name;
                })
                ->addColumn('job_category', function ($row) {
                    return $row->job_category ?? 'N/A';
                })
                ->addColumn('job_title', function ($row) {
                    if ($row->job_title_prof!=null){
                        $specialName=Specialist_job_titles::where('id',$row->job_title_prof)->first();
                        return $row->job_title .' ('.$specialName->name.')';
                    }else{
                        return $row->job_title ?? 'N/A';

                    }
                })
                ->addColumn('office_name', function ($row) {
                    return optional($row->office)->name ?? 'N/A';
                })
                ->addColumn('unit_name', function ($row) {
                    return optional($row->unit)->unit_name ?? 'N/A';
                })
                ->addColumn('postcode', function ($row) {
                    return $row->postcode ?? 'N/A';
                })
                ->addColumn('job_type', function ($row) {
                    return $row->job_type ?? 'N/A';
                })
                ->addColumn('experience', function ($row) {
                    return $row->experience ?? 'N/A';
                })
                ->addColumn('qualification', function ($row) {
                    return $row->qualification ?? 'N/A';
                })
                ->addColumn('salary', function ($row) {
                    return $row->salary ?? 'N/A';
                })
                ->addColumn('is_on_hold', function ($row) {
                    $status='';
                    if ($row->is_on_hold=="1"){

                        $status = '<h5><span class="badge badge-danger">Yes</span></h5>';
                    }else{
                        $status = '<h5><span class="badge badge-info">No</span></h5>';
                    }
                    return $status;

                })

            ->addColumn('action', function ($oRow) {
                    $auth_user = Auth::user();
                    $action = "<div class=\"btn-group\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-bs-toggle=\"dropdown\">
                <i class=\"bi bi-list\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">";
                    if ($auth_user->hasPermissionTo('sale_edit')) {
                        $action .= "<a href=\"/sales/{$oRow->id}/edit\" class=\"dropdown-item\"> Edit</a>";
                    }
                if ($auth_user->hasPermissionTo('sale_on-hold')) {
                    if ($oRow->is_on_hold=="0") {

                        $action .= "<a href=\"#\" class=\"dropdown-item\"
                        onclick=\"confirmOnHoldSale({$oRow->id})\">On Hold Sale</a>";
                    }else{
                        $action .= "<a href=\"#\" class=\"dropdown-item\"
                        onclick=\"confirmUnHoldSale({$oRow->id})\">Un Hold Sale</a>";
                    }
                }

                if ($auth_user->hasPermissionTo('sale_close')) {
                    $action .= "<a href=\"#\" class=\"dropdown-item\"
                        onclick=\"confirmCloseSale({$oRow->id})\">Close Sale</a>";
                }
                if ($auth_user->hasPermissionTo('sale_close')) {
                    $action .= "<a href=\"" . route('sales.show', ['sale' => $oRow->id]) . "\" class=\"dropdown-item\">Sale View</a>";
                }
                if ($auth_user->hasPermissionTo('sale_history')) {
                    $action .=      "<a href=\"/sale-history/{$oRow->id}\" class=\"dropdown-item\">Sale History</a>";
                }
//                    if ($auth_user->hasPermissionTo('sale_note-create')) {
//                        $action .= "<a href=\"#\" class=\"dropdown-item\"
//                        onclick=\"confirmAddNote({$oRow->id})\">Add Note</a>";
//                    }
                    $action .= "</div>
        </div>
    </div>";
                    $url = route('module-note-store');
                    $csrf = csrf_token();
                    if ($auth_user->hasPermissionTo('sale_note-create')) {
                        $action .=
                            "<div id=\"add_sale_note{$oRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-lg\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Add Sale Note</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <form action=\"{$url}\" method=\"POST\" class=\"form-horizontal\" id=\"note_form{$oRow->id}\">
                                        <input type=\"hidden\" name=\"_token\" value=\"{$csrf}\">
                                        <input type=\"hidden\" name=\"module\" value=\"Sale\">
                                        <div class=\"modal-body\">
                                            <div id=\"note_alert{$oRow->id}\"></div>
                                            <div class=\"form-group row\">
                                                <label class=\"col-form-label col-sm-3\">Details</label>
                                                <div class=\"col-sm-9\">
                                                    <input type=\"hidden\" name=\"module_key\" value=\"{$oRow->id}\">
                                                    <textarea name=\"details\" id=\"note_details{$oRow->id}\" class=\"form-control\" cols=\"30\" rows=\"4\"
                                                              placeholder=\"TYPE HERE ..\" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-link legitRipple\" data-dismiss=\"modal\">
                                                Close
                                            </button>
                                            <button type=\"submit\" data-note_key=\"{$oRow->id}\" class=\"btn bg-teal legitRipple note_form_submit\">Save</button>
                                        </div>
                                    </form>
                            </div>
                        </div>
                      </div>";
                    }


                    return $action;
                })
                ->rawColumns(['sale_added_date', 'updated_at','action','no_of_sent_cv','is_on_hold']) // Make sure to declare rawColumns for HTML content in a column

//                ->rawColumns(['action','date','unit_name','phone_number','contact_landline','type','status','notes','time'])
                ->make(true);

        }


    }
    public function getHeadUnit($headOfficeId)
    {
        // Fetch units based on the head office ID
        $units = Unit::where('head_office', $headOfficeId)->get();

        return response()->json(['units' => $units]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units = Office::join('units', 'offices.id', '=', 'units.head_office')
            ->select('units.*', 'offices.name')->where('units.status', 'active')->get();
        $head_offices = Office::where("status", "active")->get();
        return view('administrator.sales.create',compact('units','head_offices'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            date_default_timezone_set('Europe/London');
            // Laravel validation
            $validator = Validator::make($request->all(), [
//                '_token' => 'required',
                'app_job_category' => 'required|in:nurses,non-nurses',
                'job_title' => 'required',
                'head_office' => 'required',
                'head_office_unit' => 'required',
                'postcode' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = DB::table('sales')
                            ->where('job_title', $request->input('job_title'))
                            ->where('postcode', $request->input('postcode'))
                            ->where('job_category', $request->input('app_job_category'))
                            ->where('head_office', $request->input('head_office'))
                            ->where('head_office_unit', $request->input('head_office_unit'))
                            ->whereIn('status', ['active', 'pending'])
                            ->exists();

                        if ($exists) {
                            $fail('The combination of category, job title, postcode, head office, and unit has already been taken.');
                        }
                    }
                ],

                'experience' => 'required|string',
//                'job_title_special' => 'required|string',
                'salary' => 'required|string',
                'qualification' => 'required|string',
                'cv_limit' => 'required|integer',
                'benefits' => 'required|string',
                'note' => 'required|string',
            ]);

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
            // Process the form data and save to the database
            $sale = new Sale();
            $sale->user_id = Auth::id();
            $sale->job_category = $request->input('app_job_category');
            $sale->job_title = $request->input('job_title');
            $sale->head_office = $request->input('head_office');
            $sale->head_office_unit = $request->input('head_office_unit');
            $sale->postcode = $request->input('postcode');
            $sale->time = $request->input('time');
            $sale->experience = $request->input('experience');
            $sale->job_type = $request->input('job_type');
            $sale->job_title_prof = $request->input('job_title_special');
            $sale->salary = $request->input('salary');
            $sale->qualification = $request->input('qualification');
            $sale->send_cv_limit = $request->input('cv_limit');
            $sale->benefits = $request->input('benefits');
            $sale->sale_notes = $request->input('note');
            $sale->sale_added_date = date("jS F Y");
            $sale->sale_added_time = date("h:i A");
            $sale->lat = $latitude;
            $sale->lng = $longitude;
            $sale->status = 'pending';
            // Add other fields as needed
            $sale->save();
            if ($sale){
                $sale_note = new Sales_notes();
                $sale_note->sale_id = $sale->id;
                $sale_note->user_id = Auth::id();
                $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
                $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
                $sale_note->sale_note = $request->input('note');
                $sale_note->status = 'active';
                $sale_note->type_note="sale_note_pending";
                $sale_note->save();
            }

            return response()->json(['message' => 'Form submitted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        $sale=Sale::find($id);
        return view('administrator.sales.sale_detail',compact('sale'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sale=Sale::find($id);
        $units = Office::join('units', 'offices.id', '=', 'units.head_office')
            ->select('units.*', 'offices.name')->where('units.status', 'active')->get();
        $head_offices = Office::where("status", "active")->get();
        return view('administrator.sales.edit',compact('sale','units','head_offices'));
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
        try {
            date_default_timezone_set('Europe/London');
            // Laravel validation
            $sent_cv_count = CvNote::where(['sale_id' => $id, 'status' => 'active'])->count();
            $validator = Validator::make($request->all(), [
                'app_job_category' => 'required|in:nurses,non-nurses',
                'job_title' => 'required',
                'head_office' => 'required',
                'head_office_unit' => 'required',
                'postcode' => [
                    'required',
//                    function ($attribute, $value, $fail,$id) use ($request) {
//                        $exists = DB::table('sales')
//                            ->where('job_title', $request->input('job_title'))
//                            ->where('postcode', $request->input('postcode'))
//                            ->where('job_category', $request->input('app_job_category'))
//                            ->where('head_office', $request->input('head_office'))
//                            ->where('head_office_unit', $request->input('head_office_unit'))
//                            ->whereIn('status', ['active', 'pending'])
//                            ->ignore($id);
//
//                        if ($exists) {
//                            $fail('The combination of category, job title, postcode, head office, and unit has already been taken.');
//                        }
//                    }
                    Rule::unique('sales')->where( function ($query) use ($request) {
                        return $query->where('sales.job_title', $request->input('job_title'))
                            ->where('sales.postcode', $request->input('postcode'))
                            ->where('sales.job_category', $request->input('app_job_category'))
                            ->where('sales.head_office', $request->input('head_office'))
                            ->where('sales.head_office_unit', $request->input('head_office_unit'))
                            ->whereIn('sales.status', ['active','pending']);
                    })->ignore($id)
                ],
                'cv_limit' => 'required|integer|between:'.$sent_cv_count.',10',
                'experience' => 'required|string',
//                'job_title_special' => 'required|string',
                'salary' => 'required|string',
                'qualification' => 'required|string',
                'benefits' => 'required|string',
                'note' => 'required|string',
            ]);

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
            // Process the form data and save to the database
            $sale = Sale::find($id);
            $sale->user_id = Auth::id();
            $sale->job_category = $request->input('app_job_category');
            $sale->job_title = $request->input('job_title');
            $sale->head_office = $request->input('head_office');
            $sale->head_office_unit = $request->input('head_office_unit');
            $sale->postcode = $request->input('postcode');
            $sale->time = $request->input('time');
            $sale->experience = $request->input('experience');
            $sale->job_type = $request->input('job_type');
            $sale->job_title_prof = $request->input('job_title_special');
            $sale->salary = $request->input('salary');
            $sale->qualification = $request->input('qualification');
            $sale->send_cv_limit = $request->input('cv_limit');
            $sale->benefits = $request->input('benefits');
            $sale->sale_notes = $request->input('note');
            $sale->sale_added_date = date("jS F Y");
            $sale->sale_added_time = date("h:i A");
            $sale->lat = $latitude;
            $sale->lng = $longitude;
//            $sale->status = 'active';
            // Add other fields as needed
            $updated = $sale->update();
            if ($updated){
                $sale_note = new Sales_notes();
                $sale_note->sale_id = $sale->id;
                $sale_note->user_id = Auth::id();
                $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
                $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
                $sale_note->sale_note = $request->input('note');
                $sale_note->status = 'active';
                $sale_note->type_note="update_note";
                $sale_note->save();
            }

            return response()->json(['message' => 'Form submitted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
    public function closeSale(Request $request)
    {
        // Find the sale
        $sale = Sale::find($request->sale_id);

        // Check if the sale exists
        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        // Toggle the status based on the current status
        $newStatus = $sale->status === 'active' ? 'disable' : 'active';

        // Update the sale status
        $sale->update([
            'status' => $newStatus,
        ]);

        // Determine the type_note based on the new status
        $typeNote = $newStatus === 'active' ? 'open_sale_note' : 'close_sale_note';
        // Set the appropriate message based on the new status
        $message = $newStatus === 'active' ? 'Sale opened successfully' : 'Sale closed successfully';

        // Create a new sales note
        $sale_note = new Sales_notes();
        $sale_note->sale_id = $request->sale_id;
        $sale_note->user_id = Auth::id();
        $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
        $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
        $sale_note->sale_note = $request->notes;
        $sale_note->type_note = $typeNote;
        $sale_note->status = 'active';
        $sale_note->save();

        return response()->json(['message' => $message, 'new_status' => $newStatus, 'type_note' => $typeNote]);
    }

//    public function closeSale(Request $request)
//    {
//        // Implement logic to close the sale
//
//        $sale=Sale::find($request->sale_id);
//        $sale->update([
//            'status' => 'disable', // Make sure to use single quotes for string values
//        ]);
//        $sale_note = new Sales_notes();
//        $sale_note->sale_id = $request->sale_id;
//        $sale_note->user_id = Auth::id();
//        $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
//        $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
//        $sale_note->sale_note = $request->notes;
//        $sale_note->type_note="close_sale_note";
//        $sale_note->status = 'active';
//        $sale_note->save();
//
//        return response()->json(['message' => 'Sale closed successfully']);
//
//    }

    public function onHoldSale(Request $request)
    {
        try {
            date_default_timezone_set('Europe/London');
            DB::beginTransaction();
            Sale::where('id', $request->sale_id)->update(['is_on_hold' => '1']);
            $sale_note = new Sales_notes();
            $sale_note->sale_id = $request->sale_id;
            $sale_note->user_id = Auth::id();
            $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
            $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
            $sale_note->sale_note = $request->notes;
            $sale_note->status = 'active';
            $sale_note->type_note="sale_hold_note";
            $sale_note->save();
             DB::commit();
            return response()->json(['message' => 'Sale put on hold successfully']);

        }catch (\Exception $exception){
         DB::rollBack();
         return response()->json(['error' => $exception->getMessage()], 500);


        }

    }
    public function unHoldSale(Request $request)
    {
        try {
            date_default_timezone_set('Europe/London');
            DB::beginTransaction();
            Sale::where('id', $request->sale_id)->update(['is_on_hold' => '0']);
            $sale_note = new Sales_notes();
            $sale_note->sale_id = $request->sale_id;
            $sale_note->user_id = Auth::id();
            $sale_note->sales_note_added_date = Carbon::now()->format('Y-m-d');
            $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
            $sale_note->sale_note = $request->notes;
            $sale_note->status = 'active';
            $sale_note->type_note="sale_un_hold_note";
            $sale_note->save();
            DB::commit();
            return response()->json(['message' => 'Sale put on hold successfully']);

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], 500);


        }

    }
    public function getAllClosedSales()
    {
        return view('administrator.sales.close.index');
    }
    public function allClosedSales(Request $request)
    {
        if ($request->ajax()) {
            $auth_user = Auth::user();
            date_default_timezone_set('Europe/London');

            $result = Sale::with(['office', 'unit', 'user'])
                ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName', 'units.contact_email',
                    'units.unit_name', 'units.contact_phone_number')
                ->leftJoin('offices', 'offices.id', '=', 'sales.head_office')
                ->leftJoin('units', 'units.id', '=', 'sales.head_office_unit')
                ->leftJoin('users', 'users.id', '=', 'sales.user_id')
                ->where('sales.status', 'disable')->orderBy('updated_at', 'desc');
//                ->where('sales.is_on_hold', 0);


            $result->addSelect([
                'no_of_sent_cv' => CvNote::selectRaw('COUNT(*)')
                    ->whereColumn('cv_notes.sale_id', 'sales.id')
                    ->where('cv_notes.status', 'active')
                    ->getQuery()
            ]);
            $sales = $result->get();

// $sales now contains the records based on the specified conditions
            $auth_user=Auth::user();
            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('sale_added_date', function ($row) {
                    return Carbon::parse($row->sale_added_date)->format('jS F Y');
                })
                ->addColumn('sale_note', function ($row) {
                    $close_sale=Sales_notes::where('sale_id',$row->id)->orderBy('id','desc')->first();
                    if (!empty($close_sale)) {
                        return $close_sale->sale_note;
                    }else{
                        return $row->sale_note;

                    }
                })
                ->addColumn('no_of_sent_cv', function ($row) {
                    $status = $row->no_of_sent_cv == $row->send_cv_limit ?
                        '<span class="badge w-100 badge-danger" style="font-size:90%">Limit Reached</span>' :
                        "<span class='badge w-100 badge-success' style='font-size:90%'>" . ((int)$row->send_cv_limit - (int)$row->no_of_sent_cv) . " Cv's limit remaining</span>";
                    return $status;
                })
                ->addColumn('updated_at', function ($row) {
                    return Carbon::parse($row->sale_added_time)->format('h:i A');
                })
                ->addColumn('job_category', function ($row) {
                    return $row->job_category ?? 'N/A';
                })
                ->addColumn('job_title', function ($row) {
                    return $row->job_title ?? 'N/A';
                })
                ->addColumn('office_name', function ($row) {
                    return optional($row->office)->name ?? 'N/A';
                })
                ->addColumn('unit_name', function ($row) {
                    return optional($row->unit)->unit_name ?? 'N/A';
                })
                ->addColumn('postcode', function ($row) {
                    return $row->postcode ?? 'N/A';
                })
                ->addColumn('job_type', function ($row) {
                    return $row->job_type ?? 'N/A';
                })
                ->addColumn('experience', function ($row) {
                    return $row->experience ?? 'N/A';
                })
                ->addColumn('qualification', function ($row) {
                    return $row->qualification ?? 'N/A';
                })
                ->addColumn('salary', function ($row) {
                    return $row->salary ?? 'N/A';
                })

                ->addColumn('action', function ($oRow) {
                    $auth_user = Auth::user();
                    $action = "<div class=\"btn-group\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-bs-toggle=\"dropdown\">
                <i class=\"bi bi-list\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">";

                    if ($auth_user->hasPermissionTo('sale_close')) {
                        $action .= "<a href=\"#\" class=\"dropdown-item\"
                        onclick=\"confirmCloseSale({$oRow->id})\">Open Sale</a>";
                    }

//                    if ($auth_user->hasPermissionTo('sale_note-create')) {
//                        $action .= "<a href=\"#\" class=\"dropdown-item\"
//                        onclick=\"confirmAddNote({$oRow->id})\">Add Note</a>";
//                    }
                    $action .= "</div>
        </div>
    </div>";
                    $url = route('module-note-store');
                    $csrf = csrf_token();
                    if ($auth_user->hasPermissionTo('sale_note-create')) {
                        $action .=
                            "<div id=\"add_sale_note{$oRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-lg\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Add Sale Note</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <form action=\"{$url}\" method=\"POST\" class=\"form-horizontal\" id=\"note_form{$oRow->id}\">
                                        <input type=\"hidden\" name=\"_token\" value=\"{$csrf}\">
                                        <input type=\"hidden\" name=\"module\" value=\"Sale\">
                                        <div class=\"modal-body\">
                                            <div id=\"note_alert{$oRow->id}\"></div>
                                            <div class=\"form-group row\">
                                                <label class=\"col-form-label col-sm-3\">Details</label>
                                                <div class=\"col-sm-9\">
                                                    <input type=\"hidden\" name=\"module_key\" value=\"{$oRow->id}\">
                                                    <textarea name=\"details\" id=\"note_details{$oRow->id}\" class=\"form-control\" cols=\"30\" rows=\"4\"
                                                              placeholder=\"TYPE HERE ..\" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-link legitRipple\" data-dismiss=\"modal\">
                                                Close
                                            </button>
                                            <button type=\"submit\" data-note_key=\"{$oRow->id}\" class=\"btn bg-teal legitRipple note_form_submit\">Save</button>
                                        </div>
                                    </form>
                            </div>
                        </div>
                      </div>";
                    }


                    return $action;
                })
                ->rawColumns(['sale_added_date', 'updated_at','action','no_of_sent_cv','sale_note']) // Make sure to declare rawColumns for HTML content in a column

//                ->rawColumns(['action','date','unit_name','phone_number','contact_landline','type','status','notes','time'])
                ->make(true);

        }


    }
    public function saleNote($id){
        try {
            if(Request()->ajax()){
                date_default_timezone_set('Europe/London');
            $saleNotes=Sales_notes::where('sale_id',$id)->where('status','active')->orderBy('id','DESC')->get();
            return DataTables::of($saleNotes)
                ->addIndexColumn()
                ->addColumn('time', function($row){
                    $time=Carbon::parse($row->sales_note_added_time)->format('h:i A');
                    return $time;
                })
                ->addColumn('date', function($row){
                    $date=Carbon::parse($row->sales_note_added_date)->format('jS F Y');
                    return $date;
                })
                ->addColumn('type', function($row){
                    $status = '<h5><span class="badge badge-warning badge-pill">'.$row->type_note.'</span></h5>';

                    return $status;
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
                    $detail=$row->sale_note;
                    return $detail;
                })
                ->rawColumns(['action','date','user_name','status','notes','time','type'])
                ->make(true);
        }
        }catch (\Exception $exception){

        }
    }
    public function getSaleHistory($sale_history_id)
    {
        $auth_user = Auth::user()->id;
        $sale = Sale::with('office','unit')->withCount('active_cvs')->find($sale_history_id);
        if($sale->job_title_prof!='')
        {
            $sec_job_data = Specialist_job_titles::select("*")->where("id",$sale->job_title_prof)->first();
//            dd($sec_job_data);
        }
        else
        {
            $sec_job_data = $sale->job_title;
        }

        $applicants_in_crm = Client::join('crm_notes', function($join) use ($sale_history_id) {
            $join->on('crm_notes.client_id', '=', 'clients.id');
            $join->where('crm_notes.sale_id', '=', $sale_history_id);
        })
            ->join('histories', function($join) use ($sale_history_id) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->where('histories.sale_id', '=', $sale_history_id);
            })
            ->select("clients.id as app_id","clients.app_name","clients.app_job_title","clients.app_job_category","clients.app_postcode","clients.app_phone","clients.app_phoneHome",
                "crm_notes.id as note_id","crm_notes.user_id","crm_notes.client_id","crm_notes.sale_id as sale_id","crm_notes.details","crm_notes.moved_tab_to","crm_notes.crm_added_date as note_added_date",
                "crm_notes.crm_added_time as note_added_time","crm_notes.status","crm_notes.created_at","crm_notes.updated_at",
                "histories.history_added_date", "histories.sub_stage"
            )->where(array(
                'histories.status' => 'active'))
            ->whereIn('crm_notes.id', function($query) use ($sale_history_id) {
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE sale_id='.$sale_history_id.' and clients.id=client_id'));
            })
            ->get();

        $applicants_in_quality_reject = Client::join('histories', function($join) use ($sale_history_id) {
            $join->on('clients.id', '=', 'histories.client_id');
            $join->where('histories.sale_id', '=', $sale_history_id);
            $join->where('histories.sub_stage', '=', 'quality_reject');
        })
            ->join('quality_notes', function($join) use ($sale_history_id) {
                $join->on('quality_notes.client_id', '=', 'clients.id');
                $join->where('quality_notes.sale_id', '=', $sale_history_id);
            })
            ->select("clients.id as app_id","clients.app_name","clients.app_job_title","clients.app_job_category","clients.app_postcode","clients.app_phone","clients.app_phoneHome",
                "quality_notes.id as note_id","quality_notes.user_id","quality_notes.client_id","quality_notes.sale_id","quality_notes.details","quality_notes.moved_tab_to",
                "quality_notes.quality_added_date as note_added_date","quality_notes.quality_added_time as note_added_time","quality_notes.status","quality_notes.created_at","quality_notes.updated_at",
                "histories.history_added_date", "histories.sub_stage"
            )->where(array(
                'histories.status' => 'active'))
            ->whereIn('quality_notes.id', function($query) use ($sale_history_id) {
                $query->select(DB::raw('MAX(id) FROM quality_notes WHERE sale_id='.$sale_history_id.' and clients.id=client_id and moved_tab_to="rejected"'));
            })
            ->get();

        $applicants_in_quality = Client::join('histories', function($join) use ($sale_history_id) {
            $join->on('clients.id', '=', 'histories.client_id');
            $join->where('histories.sale_id', '=', $sale_history_id);
            $join->where('histories.sub_stage', '=', 'quality_cvs');
        })
            ->join('cv_notes', function($join) use ($sale_history_id) {
                $join->on('cv_notes.client_id', '=', 'clients.id');
                $join->where('cv_notes.sale_id', '=', $sale_history_id);
            })
            ->select("clients.id as app_id","clients.app_name","clients.app_job_title","clients.app_job_category","clients.app_postcode","clients.app_phone","clients.app_phoneHome",
                "cv_notes.id as note_id","cv_notes.user_id","cv_notes.client_id","cv_notes.sale_id","cv_notes.details",
                "cv_notes.send_added_date as note_added_date","cv_notes.send_added_time as note_added_time","cv_notes.status","cv_notes.created_at","cv_notes.updated_at",
                "histories.history_added_date", "histories.sub_stage"
            )->where(['histories.status' => 'active', 'cv_notes.status' => 'active'])->get();

        $applicant_crm_notes = Client::join('crm_notes', 'crm_notes.client_id', '=', 'clients.id')
            ->select("crm_notes.*", "crm_notes.sale_id as sale_id", "clients.id as app_id")
            ->where(['crm_notes.sale_id' => $sale_history_id])
            ->orderBy("crm_notes.created_at", "desc")
            ->get();

        $history_stages = '';
        $crm_stages = '';

//dd($applicant_crm_notes);
        return view('administrator.sales.sale_history',compact('applicants_in_crm', 'applicants_in_quality_reject', 'applicants_in_quality', 'applicant_crm_notes', 'history_stages', 'crm_stages', 'sale','sec_job_data'));


        // ./APPLICANT SEND AGAINST THIS JOB IN QUALITY FROM SEARCH RESULTS
    }

}
