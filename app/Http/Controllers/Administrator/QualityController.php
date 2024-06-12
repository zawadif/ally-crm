<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Client;
use App\Models\CrmNote;
use App\Models\CvNote;
use App\Models\History;
use App\Models\Office;
use App\Models\QualityNote;
use App\Models\Sale;
use App\Models\Sales_notes;
use App\Models\Specialist_job_titles;
use App\Observers\ActionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\DataTables;

class QualityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        /*** Quality CVs */
        $this->middleware('permission:quality_CVs_list|quality_CVs_cv-download|quality_CVs_job-detail|quality_CVs_cv-clear|quality_CVs_cv-reject|quality_CVs_manager-detail', ['only' => ['getAllApplicantWithSentCv']]);
        $this->middleware('permission:quality_CVs_cv-clear', ['only' => ['updateConfirmInterview']]);
        $this->middleware('permission:quality_CVs_cv-reject', ['only' => ['updateCVReject']]);
        /*** Quality CVs Rejected */
        $this->middleware('permission:quality_CVs-Rejected_list|quality_CVs-Rejected_job-detail|quality_CVs-Rejected_cv-download|quality_CVs-Rejected_manager-detail|quality_CVs-Rejected_revert-quality-cv', ['only' => ['getAllApplicantWithRejectedCv']]);
        $this->middleware('permission:quality_CVs-Rejected_revert-quality-cv', ['only' => ['revertQualityCv']]);
        /*** Quality CVs Cleared */
        $this->middleware('permission:quality_CVs-Cleared_list|quality_CVs-Cleared_job-detail|quality_CVs-Cleared_cv-download|quality_CVs-Cleared_manager-detail', ['only' => ['getAllclientsWithConfirmedInterview']]);
        /*** Common Permissions */
        $this->middleware('permission:quality_CVs_cv-download|quality_CVs-Rejected_cv-download|quality_CVs-Cleared_cv-download', ['only' => ['getDownloadApplicantCv']]);
        /*** Quality Sales */
        $this->middleware('permission:quality_Sales_list|quality_Sales_sale-clear|quality_Sales_sale-reject', ['only' => ['qualitySales','getQualitySales']]);
        $this->middleware('permission:quality_Sales_sale-clear|quality_Sales_sale-reject', ['only' => ['clearRejectSale']]);

    }


    public function qualitySales()
    {
        return view('administrator.quality.sales.index');
    }

    public function getQualitySales(Request $request)
    {

        $user = Auth::user();
        $result='';
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                // $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                // ->join('units', 'units.id', '=', 'sales.head_office_unit')
                // ->join('sales_notes', 'sales.id', '=', 'sales_notes.sale_id')
                // ->select('sales.*', 'offices.name', 'units.contact_name',
                //     'units.contact_email', 'units.unit_name', 'units.contact_phone_number', 'sales_notes.sale_note')
                // ->where(['sales.status' => 'active', 'sales.job_category' => 'nurse', 'sales_notes.status' => 'active'])->whereIn('sales.head_office', $user_permissions)->orderBy('id', 'DESC');
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->where('sales.status', '=', 'pending')->where('sales.is_on_hold', '=', '0')->whereIn('sales.head_office', $user_permissions)
                    ->orderBy('sales.updated_at', 'DESC');
            }
            else
            {
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->where('sales.status', '=', 'pending')->where('sales.is_on_hold', '=', '0')
                    ->orderBy('sales.updated_at', 'DESC');

            }

        }
        else
        {
            $auth_user = Auth::user();
            $result = Office::with('user')
                ->join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                ->where('sales.status', '=', 'pending')->where('sales.is_on_hold', '=', '0')
                ->orderBy('sales.updated_at', 'DESC');
        }


        $aColumns = ['sale_added_date', 'updated_at', 'job_category', 'job_title',
            'office_name', 'unit_name', 'postcode', 'job_type', 'experience', 'qualification', 'salary'];

        $iStart = $request->get('iDisplayStart');
        $iPageSize = $request->get('iDisplayLength');

        $order = 'id';
        $sort = ' DESC';

        if ($request->get('iSortCol_0')) { //iSortingCols

            $sOrder = "ORDER BY  ";

            for ($i = 0; $i < intval($request->get('iSortingCols')); $i++) {
                if ($request->get('bSortable_' . intval($request->get('iSortCol_' . $i))) == "true") {
                    $sOrder .= $aColumns[intval($request->get('iSortCol_' . $i))] . " " . $request->get('sSortDir_' . $i) . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = " id ASC";
            }

            $OrderArray = explode(' ', $sOrder);
            $order = trim($OrderArray[3]);
            $sort = trim($OrderArray[4]);

        }

        $sKeywords = $request->get('sSearch');
        if ($sKeywords != "") {

            $result->Where(function ($query) use ($sKeywords) {
                $query->orWhere('sales.job_title', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('unit_name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('sales.postcode', 'LIKE', "%{$sKeywords}%");
            });
        }

        for ($i = 0; $i < count($aColumns); $i++) {
            $request->get('sSearch_' . $i);
            if ($request->get('bSearchable_' . $i) == "true" && $request->get('sSearch_' . $i) != '') {
                $result->orWhere($aColumns[$i], 'LIKE', "%" . $request->orWhere('sSearch_' . $i) . "%");
            }
        }

        $iFilteredTotal = $result->count();

        if ($iStart != null && $iPageSize != '-1') {
            $result->skip($iStart)->take($iPageSize);
        }

        $result->orderBy($order, trim($sort));
        $result->limit($request->get('iDisplayLength'));
        $saleData = $result->get();

        $iTotal = $iFilteredTotal;
        $output = array(
            "sEcho" => intval($request->get('sEcho')),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $i = 0;

        foreach ($saleData as $sRow) {

            $checkbox = "<label class=\"mt-checkbox mt-checkbox-single mt-checkbox-outline\">
                             <input type=\"checkbox\" class=\"checkbox-index\" value=\"{$sRow->id}\">
                             <span></span>
                          </label>";

            $status = '<h5><span class="badge badge-warning">Pending</span></h5>';

            $url = '/clear-reject-sale';
            $csrf = csrf_token();

            $action = "<div class=\"btn-group\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-bs-toggle=\"dropdown\">
                <i class=\"bi bi-list\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">";
            if ($auth_user->hasPermissionTo('sale_edit')) {
                $action .= "<a href=\"/sales/{$sRow->id}/edit\" class=\"dropdown-item\"> Edit</a>";
            }
            if ($auth_user->hasPermissionTo('sale_view')) {
                $action .= "<a href=\"/sales/{$sRow->id}\" class=\"dropdown-item\"> View </a>";
            }
            if ($auth_user->hasAnyPermission(['quality_Sales_sale-clear', 'quality_Sales_sale-reject'])) {
                $action .=
                    "<a href=\"#\" class=\"dropdown-item\"
                                               data-controls-modal=\"#clear_reject_sale{$sRow->id}\"
                                               data-backdrop=\"static\"
                                               data-keyboard=\"false\" data-toggle=\"modal\"
                                               data-target=\"#clear_reject_sale{$sRow->id}\"
                                            > Accept/Decline </a>";
            }

            $action .=
                "</div>
                        </div>
                      </div>";
            if ($auth_user->hasAnyPermission(['quality_Sales_sale-clear', 'quality_Sales_sale-reject'])) {
                $action .=
                    "<div id=\"clear_reject_sale{$sRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-lg\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Accept/Decline Sale Notes</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <form action=\"{$url}\"
                                          method=\"POST\" class=\"form-horizontal\">
                                        <input type=\"hidden\" name=\"_token\" value=\"{$csrf}\">
                                        <div class=\"modal-body\">
                                            <div class=\"form-group row\">
                                                <label class=\"col-form-label col-sm-3\">Details</label>
                                                <div class=\"col-sm-9\">
                                                    <input type=\"hidden\" name=\"sale_id\" value=\"{$sRow->id}\">
                                                    <textarea name=\"details\" class=\"form-control\" cols=\"30\" rows=\"4\"
                                                              placeholder=\"TYPE HERE..\" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-warning legitRipple\" data-dismiss=\"modal\">
                                                Cancel
                                            </button>";
                if ($auth_user->hasPermissionTo('quality_Sales_sale-reject')) {
                    $action .= "<button type=\"submit\" name='form_action' value='sale_reject' class=\"btn btn-danger bg-orange-800 legitRipple\">Reject</button>";
                }

                if ($auth_user->hasPermissionTo('quality_Sales_sale-clear')) {
                    $action .= "<button type=\"submit\" name='form_action' value='sale_clear' class=\"btn bg-success legitRipple\">Clear</button>";
                }
                $action .=
                    "</div>
                                    </form>
                                </div>
                            </div>
                        </div>";
            }
            $action .=
                "<div id=\"manager_details{$sRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-sm\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Manager Details</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <div class=\"modal-body\">
                                        <ul class=\"list-group\">
                                            <li class=\"list-group-item active\"><p><b>Name:</b> {$sRow->contact_name}
                                            </p></li>
                                            <li class=\"list-group-item\"><p><b>Email:</b> {$sRow->contact_email}</p></li>
                                            <li class=\"list-group-item\"><p><b>Phone#:</b> {$sRow->contact_phone_number}
                                            </p></li>
                                        </ul>
                                    </div>
                                    <div class=\"modal-footer\">
                                        <button type=\"button\" class=\"btn bg-teal legitRipple\" data-dismiss=\"modal\">CLOSE
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>";
            $updated_by = Audit::join('users', 'users.id', '=', 'audits.user_id')
                ->where(['audits.auditable_id' => $sRow->id, 'audits.auditable_type' => 'App\Models\Sale'])
                ->where('audits.message', 'like', '%has been updated%')
                ->select('users.fullName')
                ->orderBy('audits.created_at', 'desc')->first();
            $updated_by = $updated_by ? $updated_by->name : $sRow->name;
            $job_title_desc='';
            if($sRow->job_title_prof!=null)
            {

                $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
            }
            else
            {
                $job_title_desc = $sRow->job_title;
            }
            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                '<span data-popup="tooltip" title="' . $sRow->name . '">' . @Carbon::parse($sRow->sale_added_date)->toFormattedDateString() . '</span>',
                '<span data-popup="tooltip" title="' . $updated_by . '">' . @Carbon::parse($sRow->updated_at)->toFormattedDateString() . '</span>',
                @$sRow->job_category,
                $job_title_desc,
                '<span data-popup="tooltip" title="' . $sRow->user->name . '">' . @$sRow->name . '</span>',
                @$sRow->unit_name,
                @$sRow->postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,
                @$status,
                @$action
            );
            $i++;
        }

        //  print_r($output);
        echo json_encode($output);
    }

    public function clearedSales()
    {
        return view('administrator.quality.sales.cleared');
    }
    public function getClearedSales(Request $request)
    {


        $user = Auth::user();
        $result='';
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                // $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                // ->join('units', 'units.id', '=', 'sales.head_office_unit')
                // ->join('sales_notes', 'sales.id', '=', 'sales_notes.sale_id')
                // ->select('sales.*', 'offices.name', 'units.contact_name',
                //     'units.contact_email', 'units.unit_name', 'units.contact_phone_number', 'sales_notes.sale_note')
                // ->where(['sales.status' => 'active', 'sales.job_category' => 'nurse', 'sales_notes.status' => 'active'])->whereIn('sales.head_office', $user_permissions)->orderBy('id', 'DESC');
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->whereIn('sales.status', ['active','disable'])->whereIn('sales.head_office', $user_permissions)
                    ->orderBy('sales.updated_at', 'DESC');
            }
            else
            {
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->whereIn('sales.status', ['active','disable'])
                    ->orderBy('sales.updated_at', 'DESC');

            }

        }
        else
        {
            $auth_user = Auth::user();
            $result = Office::with('user')
                ->join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                ->whereIn('sales.status', ['active','disable'])
                ->orderBy('sales.updated_at', 'DESC');
        }






        // $auth_user = Auth::user();
        // $result = Office::with('user')
        //     ->join('sales', 'offices.id', '=', 'sales.head_office')
        //     ->join('units', 'units.id', '=', 'sales.head_office_unit')
        //     ->join('users', 'users.id', '=', 'sales.user_id')
        //     ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
        //         'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
        //     ->whereIn('sales.status', ['active','disable'])
        //     ->orderBy('sales.updated_at', 'DESC');

        $aColumns = ['sale_added_date', 'updated_at', 'job_category', 'job_title',
            'name', 'unit_name', 'postcode', 'job_type', 'experience', 'qualification', 'salary'];

        $iStart = $request->get('iDisplayStart');
        $iPageSize = $request->get('iDisplayLength');

        $order = 'id';
        $sort = ' DESC';

        if ($request->get('iSortCol_0')) { //iSortingCols

            $sOrder = "ORDER BY  ";

            for ($i = 0; $i < intval($request->get('iSortingCols')); $i++) {
                if ($request->get('bSortable_' . intval($request->get('iSortCol_' . $i))) == "true") {
                    $sOrder .= $aColumns[intval($request->get('iSortCol_' . $i))] . " " . $request->get('sSortDir_' . $i) . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = " id ASC";
            }

            $OrderArray = explode(' ', $sOrder);
            $order = trim($OrderArray[3]);
            $sort = trim($OrderArray[4]);

        }

        $sKeywords = $request->get('sSearch');
        if ($sKeywords != "") {

            $result->Where(function ($query) use ($sKeywords) {
                $query->orWhere('job_title', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('unit_name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('sales.postcode', 'LIKE', "%{$sKeywords}%");
            });
        }

        for ($i = 0; $i < count($aColumns); $i++) {
            $request->get('sSearch_' . $i);
            if ($request->get('bSearchable_' . $i) == "true" && $request->get('sSearch_' . $i) != '') {
                $result->orWhere($aColumns[$i], 'LIKE', "%" . $request->orWhere('sSearch_' . $i) . "%");
            }
        }

        $iFilteredTotal = $result->count();

        if ($iStart != null && $iPageSize != '-1') {
            $result->skip($iStart)->take($iPageSize);
        }

        $result->orderBy($order, trim($sort));
        $result->limit($request->get('iDisplayLength'));
        $saleData = $result->get();

        $iTotal = $iFilteredTotal;
        $output = array(
            "sEcho" => intval($request->get('sEcho')),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $i = 0;

        foreach ($saleData as $sRow) {

            $checkbox = "<label class=\"mt-checkbox mt-checkbox-single mt-checkbox-outline\">
                             <input type=\"checkbox\" class=\"checkbox-index\" value=\"{$sRow->id}\">
                             <span></span>
                          </label>";

            if($sRow->status == 'active'){
                $status = '<h5><span class="badge badge-success">Active</span></h5>';
            }else{
                $status = '<h5><span class="badge badge-danger">Closed</span></h5>';
            }
            $updated_by = Audit::join('users', 'users.id', '=', 'audits.user_id')
                ->where(['audits.auditable_id' => $sRow->id, 'audits.auditable_type' => 'App\Models\Sale'])
                ->where('audits.message', 'like', '%has been updated%')
                ->select('users.fullName')
                ->orderBy('audits.created_at', 'desc')->first();
            $updated_by = $updated_by ? $updated_by->fullName : $sRow->name;
            $job_title_desc='';
            if($sRow->job_title_prof!=null)
            {

                $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
            }
            else
            {
                $job_title_desc = $sRow->job_title;
            }
            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                '<span data-popup="tooltip" title="' . $sRow->name . '">' . @Carbon::parse($sRow->sale_added_date)->toFormattedDateString() . '</span>',
                '<span data-popup="tooltip" title="' . $updated_by . '">' . @Carbon::parse($sRow->updated_at)->toFormattedDateString() . '</span>',
                @$sRow->job_category,
                $job_title_desc,
                '<span data-popup="tooltip" title="' . $sRow->user->fullName . '">' . @$sRow->name . '</span>',
                @$sRow->unit_name,
                @$sRow->postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,
                @$status
            );
            $i++;
        }
        echo json_encode($output);
    }

    public function rejectedSales()
    {
        return view('administrator.quality.sales.rejected');
    }

    public function getRejectedSales(Request $request)
    {



        $user = Auth::user();
        $result='';
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                //     $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                //     ->join('units', 'units.id', '=', 'sales.head_office_unit')
                //     ->join('sales_notes', 'sales.id', '=', 'sales_notes.sale_id')
                //     ->select('sales.*', 'offices.name', 'units.contact_name',
                //         'units.contact_email', 'units.unit_name', 'units.contact_phone_number', 'sales_notes.sale_note')
                //     ->where(['sales.status' => 'active', 'sales.job_category' => 'nurse', 'sales_notes.status' => 'active'])->whereIn('sales.head_office', $user_permissions)->orderBy('id', 'DESC');
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->where('sales.status', 'rejected')->whereIn('sales.head_office', $user_permissions)
                    ->orderBy('sales.updated_at', 'DESC');
            }
            else
            {
                $auth_user = Auth::user();
                $result = Office::with('user')
                    ->join('sales', 'offices.id', '=', 'sales.head_office')
                    ->join('units', 'units.id', '=', 'sales.head_office_unit')
                    ->join('users', 'users.id', '=', 'sales.user_id')
                    ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                        'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                    ->where('sales.status', 'rejected')
                    ->orderBy('sales.updated_at', 'DESC');

            }

        }
        else
        {
            $auth_user = Auth::user();
            $result = Office::with('user')
                ->join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
                ->where('sales.status', 'rejected')
                ->orderBy('sales.updated_at', 'DESC');
        }








        $auth_user = Auth::user();
        $result = Office::with('user')
            ->join('sales', 'offices.id', '=', 'sales.head_office')
            ->join('units', 'units.id', '=', 'sales.head_office_unit')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->select('sales.*', 'offices.name', 'units.contact_name', 'users.fullName',
                'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
            ->where('sales.status', 'rejected')
            ->orderBy('sales.updated_at', 'DESC');

        $aColumns = ['sale_added_date', 'updated_at', 'job_category', 'job_title',
            'name', 'unit_name', 'postcode', 'job_type', 'experience', 'qualification', 'salary'];

        $iStart = $request->get('iDisplayStart');
        $iPageSize = $request->get('iDisplayLength');

        $order = 'id';
        $sort = ' DESC';

        if ($request->get('iSortCol_0')) { //iSortingCols

            $sOrder = "ORDER BY  ";

            for ($i = 0; $i < intval($request->get('iSortingCols')); $i++) {
                if ($request->get('bSortable_' . intval($request->get('iSortCol_' . $i))) == "true") {
                    $sOrder .= $aColumns[intval($request->get('iSortCol_' . $i))] . " " . $request->get('sSortDir_' . $i) . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = " id ASC";
            }

            $OrderArray = explode(' ', $sOrder);
            $order = trim($OrderArray[3]);
            $sort = trim($OrderArray[4]);

        }

        $sKeywords = $request->get('sSearch');
        if ($sKeywords != "") {

            $result->Where(function ($query) use ($sKeywords) {
                $query->orWhere('sales.job_title', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('unit_name', 'LIKE', "%{$sKeywords}%");
                $query->orWhere('sales.postcode', 'LIKE', "%{$sKeywords}%");
            });
        }

        for ($i = 0; $i < count($aColumns); $i++) {
            $request->get('sSearch_' . $i);
            if ($request->get('bSearchable_' . $i) == "true" && $request->get('sSearch_' . $i) != '') {
                $result->orWhere($aColumns[$i], 'LIKE', "%" . $request->orWhere('sSearch_' . $i) . "%");
            }
        }

        $iFilteredTotal = $result->count();

        if ($iStart != null && $iPageSize != '-1') {
            $result->skip($iStart)->take($iPageSize);
        }

        $result->orderBy($order, trim($sort));
        $result->limit($request->get('iDisplayLength'));
        $saleData = $result->get();

        $iTotal = $iFilteredTotal;
        $output = array(
            "sEcho" => intval($request->get('sEcho')),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $i = 0;

        foreach ($saleData as $sRow) {



            $status = '<h5><span class="badge badge-danger">Rejected</span></h5>';

            $updated_by = Audit::join('users', 'users.id', '=', 'audits.user_id')
                ->where(['audits.auditable_id' => $sRow->id, 'audits.auditable_type' => 'Horsefly\Sale'])
                ->where('audits.message', 'like', '%has been updated%')
                ->select('users.fullName')
                ->orderBy('audits.created_at', 'desc')->first();
            $updated_by = $updated_by ? $updated_by->fullName : $sRow->name;
            $job_title_desc='';
            if($sRow->job_title_prof!=null)
            {

                $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
            }
            else
            {
                $job_title_desc = $sRow->job_title;
            }
            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                '<span data-popup="tooltip" title="' . $sRow->name . '">' . @Carbon::parse($sRow->sale_added_date)->toFormattedDateString() . '</span>',
                '<span data-popup="tooltip" title="' . $updated_by . '">' . @Carbon::parse($sRow->updated_at)->toFormattedDateString() . '</span>',
                @$sRow->job_category,
                $job_title_desc,
                '<span data-popup="tooltip" title="' . $sRow->user->fullName . '">' . @$sRow->name . '</span>',
                @$sRow->unit_name,
                @$sRow->postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,
                @$status
            );
            $i++;
        }
        echo json_encode($output);
    }

    public function getAllApplicantWithSentCv()
    {
        $user=Auth::user();

        return view('administrator.quality.cvs.sent_cv');
    }

    public function getQualityCVclients()
    {
        $user = Auth::user();
        $applicant_with_cvs='';
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                $applicant_with_cvs = Client::join('cv_notes', 'clients.id', '=', 'cv_notes.client_id')
                    ->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('sales', 'cv_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('histories', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'histories.client_id');
                        $join->on('cv_notes.sale_id', '=', 'histories.sale_id');
                    })->select('cv_notes.client_id as cv_note_app_id', 'cv_notes.details', 'cv_notes.send_added_date', 'cv_notes.send_added_time', 'cv_notes.status as cv_note_status',
                        'clients.*', 'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName')
                    ->where([
                        "clients.app_status" => "active",
                        "cv_notes.status" => "active",
                        "histories.sub_stage" => "quality_cvs", "histories.status" => "active"
                    ])->whereIn('sales.head_office', $user_permissions);
            }
            else
            {
//                dd('no admin');
                $applicant_with_cvs = Client::join('cv_notes', 'clients.id', '=', 'cv_notes.client_id')
                    ->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('sales', 'cv_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('histories', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'histories.client_id');
                        $join->on('cv_notes.sale_id', '=', 'histories.sale_id');
                    })->select('cv_notes.client_id as cv_note_app_id', 'cv_notes.details', 'cv_notes.send_added_date', 'cv_notes.send_added_time', 'cv_notes.status as cv_note_status',
                        'clients.*', 'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName')
                    ->where([
                        "clients.app_status" => "active",
                        "cv_notes.status" => "active",
                        "histories.sub_stage" => "quality_cvs", "histories.status" => "active"
                    ]);

            }

        }
        else
        {
            $applicant_with_cvs = Client::join('cv_notes', 'clients.id', '=', 'cv_notes.client_id')
                ->join('users', 'users.id', '=', 'cv_notes.user_id')
                ->join('sales', 'cv_notes.sale_id', '=', 'sales.id')
                ->join('offices', 'sales.head_office', '=', 'offices.id')
                ->join('units', 'sales.head_office_unit', '=', 'units.id')
                ->join('histories', function ($join) {
                    $join->on('cv_notes.client_id', '=', 'histories.client_id');
                    $join->on('cv_notes.sale_id', '=', 'histories.sale_id');
                })->select('cv_notes.client_id as cv_note_app_id', 'cv_notes.details', 'cv_notes.send_added_date', 'cv_notes.send_added_time', 'cv_notes.status as cv_note_status',
                    'clients.*', 'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                    'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                    'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                    'units.contact_email', 'units.website',
                    'users.fullName')
                ->where([
                    "clients.app_status" => "active",
                    "cv_notes.status" => "active",
                    "histories.sub_stage" => "quality_cvs", "histories.status" => "active"
                ]);
        }




        /*** to hide applicant cv that gets closed while still in Quality > CVs
         * ->whereIn('clients.paid_status', ['pending', 'open']);
         */
//            ->orderBy("cv_notes.created_at","desc")->get();

        $auth_user = Auth::user();
        $raw_columns = ['action'];
        $datatable = datatables()->of($applicant_with_cvs)
            ->editColumn('applicant_job_title', function ($applicant_with_cvs) {
                $job_title_desc='';
                if($applicant_with_cvs->app_job_title_prof!=null)
                {

                    $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $applicant_with_cvs->app_job_title_prof)->first();
                    $job_title_desc = $applicant_with_cvs->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {
                    $job_title_desc = $applicant_with_cvs->app_job_title;
                }

                return $job_title_desc;
            })
            ->addColumn('action', function ($applicant) use ($auth_user) {
                $content =
                    '<div class="btn-group">
                        <div class="dropdown">
                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                <i class="bi bi-list"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasPermissionTo('quality_CVs_cv-clear')) {
                    $content .=
                        '<a href="#" class="dropdown-item sms_action_option" data-controls-modal="#clear_cv' . $applicant->id . '-' . $applicant->sale_id .'" data-backdrop="static"
                                   data-keyboard="false" data-toggle="modal"
                                   data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                   data-applicantNameJs="' . $applicant->applicant_name . '"
                                    data-applicantIdJs="' . $applicant->id . '"
                                    data-target="#clear_cv' . $applicant->id . '-' . $applicant->sale_id .'">
                                    <i class="icon-file-confirm"></i>
                                    Clear Cv
                                </a>';
                }
                if ($auth_user->hasPermissionTo('quality_CVs_cv-reject')) {
                    $content .=
                        '<a href="#" class="dropdown-item" data-controls-modal="#reject_cv' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                   data-keyboard="false" data-toggle="modal" data-target="#reject_cv' . $applicant->id . '-' . $applicant->sale_id . '">
                                    <i class="icon-file-reject"></i>
                                    Reject Cv </a>';
                }

                $content .=
                    '</div>
                        </div>
                    </div>';


                /*** Manager Details Modal */
                $content .=
                    '<div id="manager_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Manager Details</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group ">
                                        <li class="list-group-item active"><p><b>Name:</b>' . $applicant->contact_name . '</p></li>
                                        <li class="list-group-item"><p><b>Email:</b>' . $applicant->contact_email . '</p></li>
                                        <li class="list-group-item"><p><b>Phone:</b>' . $applicant->contact_phone_number . '</p></li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>';
                /*** /Manager Details Modal */
                if ($auth_user->hasPermissionTo('quality_CVs_cv-clear')) {
                    $content .=
                        '<div id="clear_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title">Clear CV Notes</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="' . route('updateToInterviewConfirmed', ['id' => $applicant->id, 'viewString' => 'applicantWithSentCv']) . '" method="GET" class="form-horizontal">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="clear_details">Details</label>
                                <textarea name="details" class="form-control" id="clear_details" rows="4" placeholder="Type here..." required></textarea>
                                <input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">
                                <input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
                }

                if ($auth_user->hasPermissionTo('quality_CVs_cv-reject')) {
                    $content .=
                        '<div id="reject_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title">Reject CV Notes</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="' . route('updateToRejectedCV', ['id' => $applicant->id, 'viewString' => 'applicantWithSentCv']) . '" method="GET" class="form-horizontal">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="reject_details">Details</label>
                                <textarea name="details" class="form-control" id="reject_details" rows="4" placeholder="Type here rejection notes..." required></textarea>
                                <input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">
                                <input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
                }

//                if ($auth_user->hasPermissionTo('quality_CVs_cv-clear')) {
//                    /*** Clear CV Modal */
//                    $content .=
//                        '<div id="clear_cv' . $applicant->id . '-' . $applicant->sale_id .'" class="modal fade small_msg_modal">
//                        <div class="modal-dialog modal-lg">
//                            <div class="modal-content">
//                                <div class="modal-header">
//                                    <h5 class="modal-title">Clear CV Notes</h5>
//                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
//                                </div>
//
//                                <form action="' . route('updateToInterviewConfirmed', ['id' => $applicant->id, 'viewString' => 'applicantWithSentCv']) . '"
//                                      method="GET" class="form-horizontal msg_form_id"><input type="hidden" name="_token" value="' . csrf_token() . '">
//                                    <div class="modal-body">
//                                        <div class="form-group row">
//                                            <label class="col-form-label col-sm-3">Details</label>
//                                            <div class="col-sm-9">
//                                                <input type="hidden" name="applicant_hidden_id" id="applicant_hidden_id"  value="' . $applicant->id . '">
//                                                <input type="hidden" name="client_id_chat" id="client_id_chat">
//                                                <input type="hidden" name="applicant_name_chat" id="applicant_name_chat">
//                                                <input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">
//
//                                                <input type="hidden" name="job_hidden_id"  value="' . $applicant->sale_id . '">
//                                                <textarea name="details" class="form-control" cols="30" rows="4"
//                                                          placeholder="TYPE HERE.." required></textarea>
//                                            </div>
//                                        </div>
//                                    </div>
//
//                                    <div class="modal-footer">
//                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
//                                        <button type="submit" class="btn btn-primary">Save</button>
//                                    </div>
//                                </form>
//                            </div>
//                        </div>
//                    </div>';
//                    /*** /Clear CV Modal */
//                }
//                if ($auth_user->hasPermissionTo('quality_CVs_cv-reject')) {
//                    /*** Reject CV Modal */
//                    $content .=
//                        '<div id="reject_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">
//                        <div class="modal-dialog modal-lg">
//                            <div class="modal-content">
//                                <div class="modal-header">
//                                    <h5 class="modal-title">CV Notes</h5>
//                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
//                                </div>
//
//                                <form action="' . route('updateToRejectedCV', ['id' => $applicant->id, 'viewString' => 'applicantWithSentCv']) . '"
//                                      method="GET" class="form-horizontal"><input type="hidden" name="_token" value="' . csrf_token() . '">
//                                    <div class="modal-body">
//                                        <div class="form-group row">
//                                            <label class="col-form-label col-sm-3">Details</label>
//                                            <div class="col-sm-9">
//                                                <input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">
//                                                <input type="hidden" name="applicant_hidden_id" value="{{ $applicant->id }}">
//                                                <textarea name="details" class="form-control" cols="30" rows="4"
//                                                          placeholder="TYPE HERE Rejects notes.." required></textarea>
//                                            </div>
//                                        </div>
//                                    </div>
//
//                                    <div class="modal-footer">
//                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
//                                        <button type="submit" class="btn btn-primary">Save</button>
//                                    </div>
//                                </form>
//                            </div>
//                        </div>
//                    </div>';
//                    /*** /reject CV Modal */
//                }

                return $content;
            });

        if ($auth_user->hasPermissionTo('quality_CVs_cv-download')) {
            $datatable = $datatable->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadApplicantCv', $applicant->id) . '">
                      <span><i class="fa fa-file-download"></i></span>
                    </a>';
            });
            array_push($raw_columns, 'download');
        }
        if ($auth_user->hasPermissionTo('quality_CVs_job-detail')) {
            $datatable = $datatable->addColumn('job_details', function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details' . $applicant->id . '-' . $applicant->sale_id . '"
                                 data-backdrop="static"
                                 data-keyboard="false" data-toggle="modal"
                                 data-target="#job_details' . $applicant->id . '-' . $applicant->sale_id . '">Details</a>';
                // Job Details Modal
                $content .= '<div id="job_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg" >';
                $content .= '<div class="modal-content" >';
                $content .= '<div class="modal-header" style="background-color: #007bff; color: #ffffff;">'; // Blue header
                $content .= '<h5 class="modal-title">' . $applicant->app_name . '\'s Job Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="media flex-column flex-md-row mb-4">';
                $content .= '<div class="media-body">';
                $content .= '<div class="header-elements-sm-inline">';
                $content .= '<h5 class="media-title font-weight-semibold">';
                $content .= $applicant->name . '/' . $applicant->unit_name;
                $content .= '</h5>';
                $content .= '<div><span class="font-weight-semibold">Posted Date: </span><span class="mb-3">' . $applicant->posted_date . '</span></div>';
                $content .= '</div>';
                $content .= '<ul class="list-inline list-inline-dotted text-muted mb-0">';
                $content .= '<li class="list-inline-item">' . $applicant->job_category . ',' . $applicant->job_title . '</li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-4"><h6 class="font-weight-semibold">Job Title:</h6><p>' . $applicant->job_title . '</p></div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Postcode:</h6>
            <p class="mb-3">' . $applicant->postcode . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Job Type:</h6>
            <p class="mb-3">' . $applicant->job_type . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Timings:</h6>
            <p class="mb-3">' . $applicant->time . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Salary:</h6>
            <p class="mb-3">' . $applicant->salary . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Experience:</h6>
            <p class="mb-3">' . $applicant->experience . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Qualification:</h6>
            <p class="mb-3">' . $applicant->qualification . '</p>
            </div>';
                $content .= '<div class="col-8"> <h6 class="font-weight-semibold">Benefits:</h6>
            <p class="mb-3">' . $applicant->benefits . '</p>
            </div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn  btn-primary  legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                //<!-- /Job Details Modal -->
                return $content;
            });
            array_push($raw_columns, 'job_details');
        }

        $datatable = $datatable->addColumn('updated_cv', function ($clients) {
            return
                '<a href="' . route('downloadUpdatedApplicantCv', $clients->id) . '">
                   <span><i class="fa fa-file-upload"></i></span>
                </a>';
        });
        array_push($raw_columns, 'updated_cv');
        array_push($raw_columns, "applicant_job_title");
        $datatable=$datatable->addColumn('office_name', function ($applicant) {
            $sale = Sale::where('id', $applicant->sale_id)->first();

            if ($sale) {
                $officeName = $sale->office->name; // Assuming 'name' is the attribute in your Office model that holds the office name
                $modalId = 'officeDetailsModal_' . $applicant->id; // Unique modal ID based on applicant's ID
                // Modal form for managing office details
                $content = '<a href="#" data-toggle="modal" data-target="#' . $modalId . '">' . $officeName . '</a>';
                $content .= '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
                $content .= '<div class="modal-dialog modal-dialog-centered" role="document">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title" id="' . $modalId . 'Label">Office Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                // Assuming you have attributes like address, contact details etc. in your Office model
                $content .= '<p><strong>Contact Name:</strong> ' . $applicant->unit_name . '</p>';
                $content .= '<p><strong>Contact Email:</strong> ' . $applicant->contact_email. '</p>';
                $content .= '<p><strong>Contact Phone Number:</strong> ' . $applicant->contact_phone_number . '</p>';

                // Add more details as needed
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #336699; color: #fff;">Close</button>';

                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                return $content;
            }
            return null; // or whatever default value you want to set if the sale or office is not found
        });
        array_push($raw_columns, "office_name");


        $datatable = $datatable->addColumn('upload', function ($clients) {
            return '<a href="#" onclick="uploadCv(' . $clients->id . ')" class="import_cv" data-controls-modal="#import_applicant_cv" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#import_applicant_cv"><span><i class="fa fa-file-download"></i></span>&nbsp;</a>';

        });
        array_push($raw_columns, 'upload');
        $datatable = $datatable->rawColumns($raw_columns)
            ->make(true);
        return $datatable;
    }

    public function clearRejectSale(Request $request)
    {
        try {

            DB::beginTransaction();
            date_default_timezone_set('Europe/London');
            $notes = $request->input('details');
            $id = $request->input('sale_id');
            $auth_user = Auth::user();
            $form_action = $request->input('form_action');
            $sale = Sale::find($id);
            if ($sale) {
                $sale->update(['status' => ($form_action == 'sale_clear') ? 'active' : 'rejected']);
                $audit = new ActionObserver();
                $audit->changeSaleStatus($sale, ['status' => $sale->status]);

                Sales_notes::where('sale_id', '=', $sale->id)->where('type_note','sale_note_pending')->update(['status' => 'disable']);
                $sale_note = new Sales_notes();
                $sale_note->sale_id = $id;
                $sale_note->user_id = $auth_user->id;
                $sale_note->sales_note_added_date = \Carbon\Carbon::now()->format('Y-m-d');
                $sale_note->sales_note_added_time = Carbon::now()->format('H:i:s');
                $sale_note->sale_note = $notes;
                $sale_note->type_note = ($form_action == 'sale_clear') ? 'sale_note_cleared' : 'sale_note_rejected';
                $sale_note->status = ($form_action == 'sale_clear') ? 'active' : 'disable'; // Add this line
                $sale_note->save();

                $last_inserted_sale_note_id = $sale_note->id;
                if($last_inserted_sale_note_id > 0){
                    DB::commit();
                    return redirect()->back()->with('success', 'Sale '.(($form_action == 'sale_clear') ? 'opened' : 'rejected').' Successfully');
                }
            } else {
                return redirect()->back()->with('error', 'Sale not found!');
            }
        }catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', 'Sale not found!');

        }
    }
    public function updateConfirmInterview($client_id, $viewString)
    {
        // echo 'here';exit();

        try {
            // Start a database transaction
            DB::beginTransaction();

            date_default_timezone_set('Europe/London');
            $audit_data['action'] = "Clear";
            $details = request()->details;
            $audit_data['sale'] = $sale_id = request()->job_hidden_id;
            $user = Auth::user();
            $current_user_id = $user->id;

            // Update Client
            Client::where("id", $client_id)->update([
                'is_interview_confirm' => 'yes',
                'is_cv_in_quality_clear' => 'yes',
                'is_cv_in_quality' => 'no'
            ]);

            // Create Quality Note
            $quality_notes = new QualityNote();
            $audit_data['applicant'] = $quality_notes->client_id = $client_id;
            $quality_notes->user_id = $current_user_id;
            $quality_notes->sale_id = $sale_id;
            $audit_data['details'] = $quality_notes->details = $details;
            $audit_data['added_date'] = $quality_notes->quality_added_date = Carbon::now()->format('Y-m-d');
            $audit_data['added_time'] = $quality_notes->quality_added_time = date("h:i");
            $quality_notes->moved_tab_to = "cleared";
            $quality_notes->status = "active";
            $quality_notes->save();

            $last_inserted_note = $quality_notes->id;

            if ($last_inserted_note > 0) {
                // Create CRM Note
                $crm_notes = new CrmNote();
                $crm_notes->client_id = $client_id;
                $crm_notes->user_id = $current_user_id;
                $crm_notes->sale_id = $sale_id;
                $crm_notes->details = $details;
                $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                $crm_notes->moved_tab_to = "cv_sent";
                $crm_notes->save();

                $last_inserted_note = $crm_notes->id;

                if ($last_inserted_note > 0) {
//                    $crm_note_uid = md5($last_inserted_note);
//                    CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);

                    // Disable existing history
                    History::where([
                        "client_id" => $client_id,
                        "sale_id" => $sale_id
                    ])->update(["status" => "disable"]);

                    // Create new History
                    $history = new History();
                    $history->client_id = $client_id;
                    $history->user_id = $current_user_id;
                    $history->sale_id = $sale_id;
                    $history->stage = 'quality';
                    $history->sub_stage = 'quality_cleared';
                    $history->history_added_date = Carbon::now()->format("Y-m-d");
                    $history->history_added_time = Carbon::now()->format("H:i:s");
                    $history->status='active';
                    $history->save();

                    $last_inserted_history = $history->id;

                    if ($last_inserted_history > 0) {
                        $history_uid = md5($last_inserted_history);
                        // Perform additional operations or return a response if needed

                        // Commit the transaction
                        DB::commit();

                        return redirect()->route($viewString);
                    }
                }
            }

        } catch (\Exception $e) {
//            dd($e->getMessage());
            // An error occurred, rollback the transaction
            DB::rollback();
            // Log or handle the exception as needed
            return redirect()->route($viewString)->with('error', 'An error occurred.');
        }
    }

    public function updateCVReject($client_id, $viewString)
    {
        date_default_timezone_set('Europe/London');
        $audit_data['action'] = "Reject";
        Client::where("id", $client_id)->update(['is_CV_reject' => 'yes', 'is_cv_in_quality' => 'no']);
        $details = request()->details;
        $audit_data['sale'] = $sale_id = request()->job_hidden_id;
        $user = Auth::user();
        $current_user_id = $user->id;
        // $cv_count = Cv_note::where(['cv_notes.sale_id' => $sale_id, 'cv_notes.status' => 'active'])->count();
        // $sale_cv_count = Sale::select('send_cv_limit')->where('id',$sale_id)->first();
        // echo $cv_count.' , and sale count: '.$sale_cv_count->send_cv_limit;exit();
        $quality_notes = new QualityNote();
        $audit_data['applicant'] = $quality_notes->client_id = $client_id;
        $quality_notes->user_id = $current_user_id;
        $quality_notes->sale_id = $sale_id;
        $audit_data['details'] = $quality_notes->details = $details;
        $audit_data['added_date'] = $quality_notes->quality_added_date = Carbon::now()->format('Y-m-d');
        $audit_data['added_time'] = $quality_notes->quality_added_time = Carbon::now()->format("H:i:s");
        $quality_notes->moved_tab_to = "rejected";
        $quality_notes->status="active";
        $quality_notes->save();

        /*** activity log
         * $action_observer = new ActionObserver();
         * $action_observer->action($audit_data, 'Quality');
         */

        $last_inserted_note = $quality_notes->id;
        if ($last_inserted_note > 0) {
            CvNote::where(['sale_id' => $sale_id, 'client_id' => $client_id])->update(['status' => 'disable']);
            History::where([
                "client_id" => $client_id,
                "sale_id" => $sale_id
            ])->update(["status" => "disable"]);
            $history = new History();
            $history->client_id = $client_id;
            $history->user_id = $current_user_id;
            $history->sale_id = $sale_id;
            $history->stage = 'quality';
            $history->sub_stage = 'quality_reject';
            $history->history_added_date = Carbon::now()->format("Y-m-d");
            $history->history_added_time = Carbon::now()->format("H:i:s");
            $history->status = "active";
            $history->save();
            $last_inserted_history = $history->id;
            if ($last_inserted_history > 0) {
//                $history_uid = md5($last_inserted_history);
//                History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                return redirect()->route($viewString);
            }

        } else {
            return redirect()->route($viewString);
        }
    }

    public function getAllApplicantWithRejectedCv()
    {
        return view('administrator.quality.cvs.rejected');
    }

    public function getRejectCVClients()
    {

        $user = Auth::user();
        // echo $user->name;exit();
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            // print_r($arrays);exit();
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                date_default_timezone_set('Europe/London');
                $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                    ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('cv_notes', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                        $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                    })->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                    ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time',
                        'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_homePhone',
                        'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName','quality_user.name as quality_name')
                    ->where([
                        "clients.app_status" => "active",
                        "quality_notes.moved_tab_to" => "rejected"
                    ])->whereIn('sales.head_office', $user_permissions)
                    ->whereIn('quality_notes.id', function($query){
                        $query->select(DB::raw('MAX(id) FROM quality_notes WHERE moved_tab_to="rejected" and sale_id=sales.id and client_id=clients.id'));
                    });
            }
            else
            {
                date_default_timezone_set('Europe/London');
                $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                    ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('cv_notes', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                        $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                    })->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                    ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time',
                        'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_phoneHome',
                        'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName')
                    ->where([
                        "clients.app_status" => "active",
                        "quality_notes.moved_tab_to" => "rejected"
                    ])
                    ->whereIn('quality_notes.id', function($query){
                        $query->select(DB::raw('MAX(id) FROM quality_notes WHERE moved_tab_to="rejected" and sale_id=sales.id and client_id=clients.id'));
                    });

            }

        }
        else
        {
            date_default_timezone_set('Europe/London');
            $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                ->join('offices', 'sales.head_office', '=', 'offices.id')
                ->join('units', 'sales.head_office_unit', '=', 'units.id')
                ->join('cv_notes', function ($join) {
                    $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                    $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                })->join('users', 'users.id', '=', 'cv_notes.user_id')
                ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time',
                    'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_phoneHome',
                    'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                    'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                    'offices.name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                    'units.contact_email', 'units.website',
                    'users.fullName')
                ->where([
                    "clients.app_status" => "active",
                    "quality_notes.moved_tab_to" => "rejected"
                ])
                ->whereIn('quality_notes.id', function($query){
                    $query->select(DB::raw('MAX(id) FROM quality_notes WHERE moved_tab_to="rejected" and sale_id=sales.id and client_id=clients.id'));
                });
        }


        // date_default_timezone_set('Europe/London');
        // $applicant_with_cvs = Applicant::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
        //     ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
        //     ->join('offices', 'sales.head_office', '=', 'offices.id')
        //     ->join('units', 'sales.head_office_unit', '=', 'units.id')
        //     ->join('cv_notes', function ($join) {
        //         $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
        //         $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
        //     })->join('users', 'users.id', '=', 'cv_notes.user_id')
        //     ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time','quality_notes.created_at',
        //         'clients.id', 'clients.applicant_name', 'clients.applicant_job_title','clients.job_title_prof', 'clients.applicant_postcode', 'clients.applicant_phone', 'clients.applicant_homePhone',
        //         'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
        //         'sales.timing', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
        //         'offices.office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
        //         'units.contact_email', 'units.website',
        //         'users.name')
        //     ->where([
        //         "clients.status" => "active",
        //         "quality_notes.moved_tab_to" => "rejected"
        //     ])
        //     ->whereIn('quality_notes.id', function($query){
        //         $query->select(DB::raw('MAX(id) FROM quality_notes WHERE moved_tab_to="rejected" and sale_id=sales.id and client_id=clients.id'));
        //     });

        /*** not used
         * $status = array();
         * $x = 0;
         * foreach ($applicant_with_cvs as $applicant) {
         * $status[$x] = "rejected";
         * if ($applicant->is_interview_attend == "yes") {
         * $status[$x] = "attended";
         * } else if ($applicant->is_interview_confirm == "yes") {
         * $status[$x] = "confirmed";
         * }
         * $x++;
         * }
         *** add 'status' in compact()
         */

        $auth_user = Auth::user();
        $raw_columns = ['action'];
        $datatable = datatables()->of($applicant_with_cvs)
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {

                    $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {
                    $job_title_desc = $applicant->app_job_title;
                }

                return $job_title_desc;
            })
            ->addColumn('action', function ($applicant) use ($auth_user) {
                $content = '
        <div class="btn-group">
            <div class="dropdown">
                <a href="#" class="list-icons-item" data-toggle="dropdown">
                    <i class="bi bi-list"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">';

                if ($auth_user->hasPermissionTo('quality_CVs-Rejected_revert-quality-cv')) {
                    $content .= '
            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#revert_to_quality_cvs' . $applicant->id . '-' . $applicant->sale_id . '">
                <i class="icon-file-confirm"></i> Forward Active CV
            </a>';
                }

                $content .= '
                </div>
            </div>
        </div>';

                // Manager Details Modal
                $content .= '
        <div id="manager_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title">Manager Details</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group ">
                            <li class="list-group-item active"><p><b>Name:</b> ' . $applicant->contact_name . '</p></li>
                            <li class="list-group-item"><p><b>Email:</b> ' . $applicant->contact_email . '</p></li>
                            <li class="list-group-item"><p><b>Phone:</b> ' . $applicant->contact_phone_number . '</p></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>';

                // Revert To Quality > CVs Modal
                if ($auth_user->hasPermissionTo('quality_CVs-Rejected_revert-quality-cv')) {
                    $content .= '
        <div id="revert_to_quality_cvs' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title">CV Notes</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="' . route('revertQualityCv') . '" method="POST" class="form-horizontal">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <div class="modal-body">
                            <div class="form-group">
                                <textarea name="details" class="form-control text-center" style="resize: none;" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>
                                <input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">
                                <input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="rejected_cv_revert_to_quality_cvs" value="rejected_cv_revert_to_quality_cvs" class="btn btn-success legitRipple">Active CV</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
                }

                return $content;
            });
        if ($auth_user->hasPermissionTo('quality_CVs-Rejected_job-detail')) {
            $datatable = $datatable->addColumn('job_details', function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details' . $applicant->id . '-' . $applicant->sale_id . '"
                                 data-backdrop="static"
                                 data-keyboard="false" data-toggle="modal"
                                 data-target="#job_details' . $applicant->id . '-' . $applicant->sale_id . '">Details</a>';
                // Job Details Modal
                $content .= '<div id="job_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg" >';
                $content .= '<div class="modal-content" >';
                $content .= '<div class="modal-header bg-primary text-light" style="background-color: #007bff; color: #ffffff;">'; // Blue header
                $content .= '<h5 class="modal-title">' . $applicant->app_name . '\'s Job Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="media flex-column flex-md-row mb-4">';
                $content .= '<div class="media-body">';
                $content .= '<div class="header-elements-sm-inline">';
                $content .= '<h5 class="media-title font-weight-semibold">';
                $content .= $applicant->name . '/' . $applicant->unit_name;
                $content .= '</h5>';
                $content .= '<div><span class="font-weight-semibold">Posted Date: </span><span class="mb-3">' . $applicant->posted_date . '</span></div>';
                $content .= '</div>';
                $content .= '<ul class="list-inline list-inline-dotted text-muted mb-0">';
                $content .= '<li class="list-inline-item">' . $applicant->job_category . ',' . $applicant->job_title . '</li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-4"><h6 class="font-weight-semibold">Job Title:</h6><p>' . $applicant->job_title . '</p></div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Postcode:</h6>
            <p class="mb-3">' . $applicant->postcode . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Job Type:</h6>
            <p class="mb-3">' . $applicant->job_type . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Timings:</h6>
            <p class="mb-3">' . $applicant->time . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Salary:</h6>
            <p class="mb-3">' . $applicant->salary . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Experience:</h6>
            <p class="mb-3">' . $applicant->experience . '</p>
            </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Qualification:</h6>
            <p class="mb-3">' . $applicant->qualification . '</p>
            </div>';
                $content .= '<div class="col-8"> <h6 class="font-weight-semibold">Benefits:</h6>
            <p class="mb-3">' . $applicant->benefits . '</p>
            </div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #336699; color: #fff;">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                //<!-- /Job Details Modal -->
                return $content;
            });
            array_push($raw_columns, 'job_details');
        }



        if ($auth_user->hasPermissionTo('quality_CVs-Rejected_cv-download')) {
            $datatable = $datatable->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadUpdatedApplicantCv', $applicant->id) . '">
                     <span><i class="fa fa-file-upload"></i></span>
                    </a>';
            });
            array_push($raw_columns, 'download');
        }
        $datatable->addColumn('quality_added_date', function ($applicant) {
            return '<span data-popup="tooltip" title="'.$applicant->name.'">'.@Carbon::parse($applicant->created_at)->toFormattedDateString().'</span>';


        });
        $datatable=$datatable->addColumn('office_name', function ($applicant) {
            $sale = Sale::where('id', $applicant->sale_id)->first();

            if ($sale) {
                $officeName = $sale->office->name; // Assuming 'name' is the attribute in your Office model that holds the office name
                $modalId = 'officeDetailsModal_' . $applicant->id; // Unique modal ID based on applicant's ID
                // Modal form for managing office details
                $content = '<a href="#" data-toggle="modal" data-target="#' . $modalId . '">' . $officeName . '</a>';
                $content .= '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
                $content .= '<div class="modal-dialog modal-dialog-centered" role="document">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title" id="' . $modalId . 'Label">Office Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                // Assuming you have attributes like address, contact details etc. in your Office model
                $content .= '<p><strong>Contact Name:</strong> ' . $applicant->unit_name . '</p>';
                $content .= '<p><strong>Contact Email:</strong> ' . $applicant->contact_email. '</p>';
                $content .= '<p><strong>Contact Phone Number:</strong> ' . $applicant->contact_phone_number . '</p>';

                // Add more details as needed
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #336699; color: #fff;">Close</button>';

                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                return $content;
            }
            return null; // or whatever default value you want to set if the sale or office is not found
        });
        array_push($raw_columns, "office_name");
        array_push($raw_columns, "quality_added_date");
        array_push($raw_columns, "applicant_job_title");
        $datatable = $datatable->rawColumns($raw_columns)
            ->make(true);
        return $datatable;
    }

    public function revertQualityCv(Request $request)
    {
        $auth_user = Auth::user();
        date_default_timezone_set('Europe/London');
        $audit_data['action'] = "Revert Quality > Rejected CV";
        $details = request('details');
        $applicant_id = request('applicant_hidden_id');
        $audit_data['sale'] = $sale_id = request('job_hidden_id');
        $cv_count = CvNote::where(['cv_notes.sale_id' => $sale_id, 'cv_notes.status' => 'active'])->count();
        $sale_cv_count = Sale::select('send_cv_limit')->where('id',$sale_id)->first();
        // echo $test.' , and sale count: '.$sale_count->send_cv_limit;exit();


        if($cv_count >=  $sale_cv_count->send_cv_limit)
        {
            toastr()->error('Sale cv limit exceeds.');
            return redirect()->back()->with('error', 'Sale cv limit exceeds.');

        }
        QualityNote::where(['client_id' => $applicant_id, 'sale_id' => $sale_id, 'moved_tab_to' => 'rejected'])->delete();

        $date_now = Carbon::now();
        $update_cv_note = CvNote::where(['sale_id' => $sale_id, 'client_id' => $applicant_id])->orderBy('id', 'desc')
            ->limit(1)->update([
                'user_id'=>Auth::id(),
                'status' => 'active'
            ]);

        if ($update_cv_note) {
            History::where([
                "client_id" => $applicant_id,
                "sale_id" => $sale_id
            ])->update(["status" => "disable"]);
            $history = new History();
            $history->client_id = $applicant_id;
            $history->user_id = $auth_user->id;
            $history->sale_id = $sale_id;
            $history->stage = 'quality';
            $history->sub_stage = 'quality_cvs';
            $history->history_added_date = Carbon::now()->format("Y-m-d");
            $history->history_added_time = Carbon::now()->format("H:i:s");
            $history->status = 'active';
            $history->save();
            $last_inserted_history = $history->id;
            if ($last_inserted_history > 0) {
//                $history_uid = md5($last_inserted_history);
//                History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                return redirect()->back()->with('qualityApplicantMsg', 'Applicant has been sent to quality');
            }

        } else {
            return redirect()->back()->with('qualityApplicantErr', 'Applicant Cant be Sent');
        }
    }

    public function getAllApplicantsWithConfirmedInterview()
    {
//        dd('sad');
        return view('administrator.quality.cvs.confirmed');
    }



    public function getConfirmCVApplicants()
    {

        $user = Auth::user();
        $applicant_with_cvs='';
        if($user->is_admin!=='1')
        {
            $permissions = $user->getAllPermissions()->pluck('name', 'id');
            $arrays = @json_decode(json_encode($permissions), true);
            $user_permissions = array();
            foreach($arrays as $per){
                if(str_contains($per, 'Hoffice_'))
                {
                    $res = explode("-", $per);
                    $user_permissions[]=$res[1];

                }
            }
            if(isset($user_permissions) && count($user_permissions)>0)
            {
                $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                    ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('cv_notes', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                        $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                    })->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                    ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time', 'quality_notes.created_at',
                        'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_phoneHome',
                        'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name',
                        'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName')
                    ->where([
                        "clients.app_status" => "active",
                        "quality_notes.moved_tab_to" => "cleared"
                    ])->whereIn('sales.head_office', $user_permissions);

            }
            else
            {
                $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                    ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                    ->join('offices', 'sales.head_office', '=', 'offices.id')
                    ->join('units', 'sales.head_office_unit', '=', 'units.id')
                    ->join('cv_notes', function ($join) {
                        $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                        $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                    })->join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                    ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time', 'quality_notes.created_at',
                        'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_phoneHome',
                        'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                        'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                        'offices.name', 'units.unit_name',
                        'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                        'units.contact_email', 'units.website',
                        'users.fullName')
                    ->where([
                        "clients.app_status" => "active",
                        "quality_notes.moved_tab_to" => "cleared"
                    ]);

            }

        }
        else
        {
            $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
                ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
                ->join('offices', 'sales.head_office', '=', 'offices.id')
                ->join('units', 'sales.head_office_unit', '=', 'units.id')
                ->join('cv_notes', function ($join) {
                    $join->on('cv_notes.client_id', '=', 'quality_notes.client_id');
                    $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
                })->join('users', 'users.id', '=', 'cv_notes.user_id')
                ->join('users as quality_user', 'quality_user.id', '=', 'quality_notes.user_id')
                ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time', 'quality_notes.created_at',
                    'clients.id', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_postcode', 'clients.app_phone', 'clients.app_phoneHome',
                    'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                    'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                    'offices.name', 'units.unit_name',
                    'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                    'units.contact_email', 'units.website',
                    'users.fullName')
                ->where([
                    "clients.app_status" => "active",
                    "quality_notes.moved_tab_to" => "cleared"
                ]);

        }


        // $applicant_with_cvs = Applicant::join('quality_notes', 'applicants.id', '=', 'quality_notes.applicant_id')
        //     ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
        //     ->join('offices', 'sales.head_office', '=', 'offices.id')
        //     ->join('units', 'sales.head_office_unit', '=', 'units.id')
        //     ->join('cv_notes', function ($join) {
        //         $join->on('cv_notes.applicant_id', '=', 'quality_notes.applicant_id');
        //         $join->on('cv_notes.sale_id', '=', 'quality_notes.sale_id');
        //     })->join('users', 'users.id', '=', 'cv_notes.user_id')
        //     ->select('quality_notes.details', 'quality_notes.quality_added_date', 'quality_notes.quality_added_time', 'quality_notes.created_at',
        //         'applicants.id', 'applicants.applicant_name', 'applicants.applicant_job_title', 'applicants.applicant_postcode', 'applicants.applicant_phone', 'applicants.applicant_homePhone',
        //         'sales.id as sale_id', 'sales.job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
        //         'sales.timing', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
        //         'offices.office_name', 'units.unit_name',
        //         'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
        //         'units.contact_email', 'units.website',
        //         'users.name')
        //     ->where([
        //         "applicants.status" => "active",
        //         "quality_notes.moved_tab_to" => "cleared"
        //     ]);

        $auth_user = Auth::user();
        $raw_columns = ['action'];
        $datatable = datatables()->of($applicant_with_cvs)
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {

                    $job_prof_res = Specialist_job_titles::select('id','name')->where("id", $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {
                    $job_title_desc = $applicant->app_job_title;
                }

                return $job_title_desc;
            })
            ->addColumn('action', function ($applicant) {
                return
                    '<div class="btn-group">
                        <div class="dropdown">
                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                <i class="bi bi-list"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                            </div>
                        </div>
                    </div>

                    <!-- Manager Details Modal -->
                    <div id="manager_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Manager Details</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group ">
                                        <li class="list-group-item active"><p><b>Name:</b> ' . $applicant->contact_name . '</p></li>
                                        <li class="list-group-item"><p><b>Email:</b> ' . $applicant->contact_email . '</p></li>
                                        <li class="list-group-item"><p><b>Phone:</b> ' . $applicant->contact_phone_number . '</p></li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Manager Details Modal -->
                ';
            });
        if ($auth_user->hasPermissionTo('quality_CVs-Cleared_cv-download')) {
            $datatable = $datatable->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-download"></i></span>
                    </a>';
            });
            array_push($raw_columns, 'download');
        }
        if ($auth_user->hasPermissionTo('quality_CVs-Cleared_job-detail')) {
            $datatable = $datatable->addColumn('job_details', function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details' . $applicant->id . '-' . $applicant->sale_id . '"
                                         data-backdrop="static"
                                         data-keyboard="false" data-toggle="modal"
                                         data-target="#job_details' . $applicant->id . '-' . $applicant->sale_id . '">Details</a>';
                //<!-- Job Details Modal -->
                $content .= '<div id="job_details' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">' . $applicant->app_name . '\'s Job Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="media flex-column flex-md-row mb-4">';
                $content .= '<div class="media-body">';
                $content .= '<div class=" header-elements-sm-inline">';
                $content .= '<h5 class="media-title font-weight-semibold">';
                $content .= $applicant->name . '/' . $applicant->unit_name;
                $content .= '</h5>';
                $content .= '<div><span class="font-weight-semibold">Posted Date: </span><span class="mb-3">' . $applicant->posted_date . '</span></div>';
                $content .= '</div>';
                $content .= '<ul class="list-inline list-inline-dotted text-muted mb-0">';
                $content .= '<li class="list-inline-item">' . $applicant->job_category . ',' . $applicant->job_title . '</li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-4"><h6 class="font-weight-semibold">Job Title:</h6><p>' . $applicant->job_title . '</p></div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Postcode:</h6>
                    <p class="mb-3">' . $applicant->postcode . '</p>
                    </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Job Type:</h6>
                    <p class="mb-3">' . $applicant->job_type . '</p>
                    </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Timings:</h6>
                    <p class="mb-3">' . $applicant->time . '</p>
                    </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Salary:</h6>
                    <p class="mb-3">' . $applicant->salary . '</p>
                    </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Experience:</h6>
                    <p class="mb-3">' . $applicant->experience . '</p>
                    </div>';
                $content .= '<div class="col-4"> <h6 class="font-weight-semibold">Qualification:</h6>
                    <p class="mb-3">' . $applicant->qualification . '</p>
                    </div>';
                $content .= '<div class="col-8"> <h6 class="font-weight-semibold">Benefits:</h6>
                    <p class="mb-3">' . $applicant->benefits . '</p>
                    </div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #336699; color: #fff;">Close</button>
                    </div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                //<!-- /Job Details Modal -->
                return $content;
            });

            array_push($raw_columns, 'job_details');
        }
        $datatable->addColumn('quality_added_date', function ($applicant) {
            return '<span data-popup="tooltip" title="'.$applicant->quality_name.'">'.@Carbon::parse($applicant->created_at)->toFormattedDateString().'</span>';


        });
        $datatable=$datatable->addColumn('office_name', function ($applicant) {
            $sale = Sale::where('id', $applicant->sale_id)->first();

            if ($sale) {
                $officeName = $sale->office->name; // Assuming 'name' is the attribute in your Office model that holds the office name
                $modalId = 'officeDetailsModal_' . $applicant->id; // Unique modal ID based on applicant's ID
                // Modal form for managing office details
                $content = '<a href="#" data-toggle="modal" data-target="#' . $modalId . '">' . $officeName . '</a>';
                $content .= '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
                $content .= '<div class="modal-dialog modal-dialog-centered" role="document">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title" id="' . $modalId . 'Label">Office Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                // Assuming you have attributes like address, contact details etc. in your Office model
                $content .= '<p><strong>Contact Name:</strong> ' . $applicant->unit_name . '</p>';
                $content .= '<p><strong>Contact Email:</strong> ' . $applicant->contact_email. '</p>';
                $content .= '<p><strong>Contact Phone Number:</strong> ' . $applicant->contact_phone_number . '</p>';

                // Add more details as needed
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #336699; color: #fff;">Close</button>';

                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                return $content;
            }
            return null; // or whatever default value you want to set if the sale or office is not found
        });
        array_push($raw_columns, "office_name");


        array_push($raw_columns, "quality_added_date");
        array_push($raw_columns, "applicant_job_title");
        $datatable = $datatable->rawColumns($raw_columns)
            ->make(true);

        return $datatable;
    }



    public function revert_cv_in_quality($applicant_cv_id)
    {
        try {
            DB::beginTransaction();

            $audit_data['action'] = "Send CV";
            date_default_timezone_set('Europe/London');
            $audit_data['sale'] = $sale = request()->job_hidden_id;
            $detail_note = request()->details;

            $is_cv_to_quality = '';
            $applicant_object = Client::find($applicant_cv_id);
            $is_cv_to_quality = $applicant_object->is_cv_in_quality;


            Client::where("id", $applicant_object->id)->update([
                'is_interview_confirm' => 0,
                'is_cv_in_quality_clear' => 0,
                'is_cv_in_quality' => 1
            ]);

            $user = Auth::user();
            $current_user_id = $user->id;

            CvNote::where([
                "client_id" => $applicant_object->id,
                "sale_id" => $sale
            ])->orderBy('id', 'desc')
                ->take(1)
                ->update(["details" => $detail_note,'status'=>'reject']);

            $quality_notes = QualityNote::where([
                "client_id" => $applicant_object->id,
                "sale_id" => $sale
            ])->get();

            if ($quality_notes) {
                QualityNote::where([
                    "client_id" => $applicant_object->id,
                    "sale_id" => $sale
                ])->update(["status" => "disable"]);
                QualityNote::create([
                    "client_id" => $applicant_object->id,
                    "sale_id" => $sale,
                    'user_id'=>Auth::id(),
                    "moved_tab_to" => "rejected" ,
                    'status'=>'active',
                    'details'=>$detail_note,
                    'quality_added_date'=> Carbon::now()->format("Y-m-d"),
                    'quality_added_time'=> Carbon::now()->format("H:i:s")
                ]);
            }

            $crm_notes = CrmNote::where([
                "client_id" => $applicant_object->id,
                "sale_id" => $sale
            ])->get();

            if (!empty($crm_notes)) {
                CrmNote::where([
                    "client_id" => $applicant_object->id,
                    "sale_id" => $sale
                ])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved"])
                    ->update(["status" => "disable"]);
            }

            History::where([
                "client_id" => $applicant_object->id,
                "sale_id" => $sale
            ])->update(["status" => "disable"]);

            $history = new History();
            $history->client_id = $applicant_object->id;
            $history->user_id = $current_user_id;
            $history->sale_id = $sale;
            $audit_data['stage'] = $history->stage = 'quality';
            $audit_data['sub_stage'] = $history->sub_stage = 'quality_reject';
            $history->history_added_date = Carbon::now()->format("Y-m-d");
            $history->history_added_time = Carbon::now()->format("H:i:s");
            $history->status='active';
            $history->save();

            $last_inserted_history = $history->id;

            if ($last_inserted_history > 0) {
                $history_uid = md5($last_inserted_history);
            }

            DB::commit();

            return Redirect::back()->with('success', 'Client is reverted back in quality cvs tab.');

        } catch (\Exception $e) {
//            dd($e->getMessage());
            DB::rollback();
            return Redirect::back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }






}
