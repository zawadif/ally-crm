<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\ApplicantNote;
use App\Models\Applicants_pivot_sales;
use App\Models\Client;
use App\Models\CrmNote;
use App\Models\CvNote;
use App\Models\ModuleNote;
use App\Models\Office;
use App\Models\Sale;
use App\Models\Sales_notes;
use App\Models\Specialist_job_titles;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ResourceController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
        /*** Sub-Links Permissions */
        $this->middleware('permission:resource_Nurses-list', ['only' => ['getNurseSales','getNursingJob']]);
        $this->middleware('permission:resource_Non-Nurses-list', ['only' => ['getNonNurseSales','getNonNursingJob']]);
//        $this->middleware('permission:resource_Non-Nurses-specialist', ['only' => ['getNonNurseSpecialistSales','getNonNursingSpecialistJob']]);
        $this->middleware('permission:resource_Last-7-Days-Applicants', ['only' => ['getLast7DaysApplicantAdded','get7DaysApplicants']]);
        $this->middleware('permission:resource_Last-21-Days-Applicants', ['only' => ['getLast21DaysApplicantAdded','get21DaysApplicants']]);
        $this->middleware('permission:resource_All-Applicants', ['only' => ['getLast2MonthsApplicantAdded','get2MonthsApplicant']]);
        $this->middleware('permission:resource_Crm-All-Rejected-Applicants', ['only' => ['getAllCrmRejectedApplicantCv','allCrmRejectedApplicantCvAjax']]);
        $this->middleware('permission:resource_Crm-Rejected-Applicants', ['only' => ['getCrmRejectedApplicantCv','getCrmRejectedApplicantCvAjax']]);
        $this->middleware('permission:resource_Crm-Request-Rejected-Applicants', ['only' => ['getCrmRequestRejectedApplicantCv','getCrmRequestRejectedApplicantCvAjax']]);
        $this->middleware('permission:resource_Crm-Not-Attended-Applicants', ['only' => ['getCrmNotAttendedApplicantCv','getCrmNotAttendedApplicantCvAjax']]);
        $this->middleware('permission:resource_Crm-Start-Date-Hold-Applicants', ['only' => ['getCrmStartDateHoldApplicantCv','getCrmStartDateHoldApplicantCvAjax']]);
        $this->middleware('permission:resource_Crm-Paid-Applicants', ['only' => ['getCrmPaidApplicantCv','getCrmPaidApplicantCvAjax']]);
        /*** Callback Permissions */
        $this->middleware('permission:resource_Potential-Callback_list|resource_Potential-Callback_revert-callback', ['only' => ['potentialCallBackclients','getPotentialCallBackclients']]);
        $this->middleware('permission:resource_Potential-Callback_revert-callback', ['only' => ['getApplicantRevertToSearchList']]);
        $this->middleware('permission:applicant_export', ['only' => ['export_7_days_clients_date','export_Last21DaysApplicantAdded','export_Last2MonthsApplicantAdded',
            'export_15_km_clients','exportAllCrmRejectedApplicantCv','Export_CrmRejectedApplicantCv','getCrmRequestRejectedApplicantCv','exportCrmNotAttendedApplicantCv'
            ,'exportCrmStartDateHoldApplicantCv','exportCrmPaidApplicantCv','exportPotentialCallBackclients']]);

    }

    public function getNurseSales()
    {
        // $sales = Office::join('sales', 'offices.id', '=', 'sales.head_office')
        //     ->join('units', 'units.id', '=', 'sales.head_office_unit')
        //     ->select('sales.*', 'offices.office_name', 'units.contact_name',
        //         'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
        //     ->where(['sales.status' => 'active', 'sales.job_category' => 'nurse'])->get();
        $value = '0';
        return view('administrator.resource.nursing', compact('value'));
    }

    public function getNursingJob(Request $request)
    {

        $user = Auth::user();
        $result='';


            $sale_notes = Sales_notes::select('sale_id','sales_notes.sale_note', DB::raw('MAX(created_at) as
            sale_created_at'))
                ->groupBy('sale_id');
            $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                ->select('sales.*', 'offices.name', 'units.contact_name',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number',DB::raw("(SELECT count(cv_notes.sale_id) from cv_notes
                 WHERE cv_notes.sale_id=sales.id AND cv_notes.status='active' group by cv_notes.sale_id) as sale_count"))
                ->where(['sales.status' => 'active', 'sales.is_on_hold' => '0', 'sales.job_category' => 'nurses'])
                ->orderBy('sales.id', 'DESC');

        $aColumns = ['sale_added_date', 'sale_added_time', 'job_title', 'name', 'unit_name',
            'postcode', 'job_type', 'experience', 'qualification', 'salary', 'sale_note','Cv Limit'];

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

            $postcode = "<a href=\"/clients-within-15-km/{$sRow->id}\">{$sRow->postcode}</a>";

            $action = "<div class=\"list-icons\">
            <div class=\"dropdown\">
                <a href=\"#\" class=\"list-icons-item\" data-toggle=\"dropdown\">
                    <i class=\"icon-menu9\"></i>
                </a>
                <div class=\"dropdown-menu dropdown-menu-right\">
                    <a href=\"#\" class=\"dropdown-item\"
                                               data-controls-modal=\"#manager_details{$sRow->id}\"
                                               data-backdrop=\"static\"
                                               data-keyboard=\"false\" data-toggle=\"modal\"
                                               data-target=\"#manager_details{$sRow->id}\"
                                            > Manager Details </a>
                </div>
            </div>
          </div>
          <div id=\"manager_details{$sRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-sm\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Manager Details</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <div class=\"modal-body\">
                                        <ul class=\"list-group\">
                                            <li class=\"list-group-item active\"><p><b>Name: </b>{$sRow->contact_name}</p>
                                            </li>
                                            <li class=\"list-group-item\"><p><b>Email: </b>{$sRow->contact_email}</p></li>
                                            <li class=\"list-group-item\"><p><b>Phone#: </b>{$sRow->contact_phone_number}</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class=\"modal-footer\">
                                        <button type=\"button\" class=\"btn bg-teal legitRipple\" data-dismiss=\"modal\">CLOSE
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>";

//            $history = "<a href=\"#\" class=\"reject_history\" data-applicant=\"{$result->id}\"
//                                 data-controls-modal=\"#reject_history{$result->id}\"
//                                 data-backdrop=\"static\" data-keyboard=\"false\" data-toggle=\"modal\"
//                                 data-target=\"#reject_history{$result->id}\">History</a>
//                        <div id=\"reject_history{$result->id}\" class=\"modal fade\" tabindex=\"-1\">
//                            <div class=\"modal-dialog modal-lg\">
//                                <div class=\"modal-content\">
//                                    <div class=\"modal-header\">
//                                        <h6 class=\"modal-title\">Rejected History - <span class=\"font-weight-semibold\">{$result->applicant_name}</span></h6>
//                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
//                                    </div>
//                                    <div class=\"modal-body\" id=\"applicant_rejected_history{$result->id}\" style=\"max-height: 500px; overflow-y: auto;\">
//                                    </div>
//                                    <div class=\"modal-footer\">
//                                        <button type=\"button\" class=\"btn bg-teal legitRipple\" data-dismiss=\"modal\">Close</button>
//                                    </div>
//                                </div>
//                            </div>
//                        </div>";
            $job_title_desc='';
            if(@$sRow->job_title_prof!='')
            {
                $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
                // $job_title_desc = @$sRow->job_title.' ('.@$sRow->job_title_prof.')';
            }
            else
            {
                $job_title_desc = @$sRow->job_title;
            }


            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                @$sRow->sale_added_date,
                @$sRow->sale_added_time,
                $job_title_desc,
                @$sRow->name,
                @$sRow->unit_name,
                @$postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,

                @$sRow->result==$sRow->send_cv_limit?'<span style="color:red;">Limit Reached</span>':"<span style='color:green'>".((int)$sRow->send_cv_limit - (int)$sRow->result)." Cv's limit remaining</span>",
            );
            $i++;
        }

        //  print_r($output);
        echo json_encode($output);
    }
    public function get15kmclients($id,$radius=null)
    {
//        dd($id);
        // echo $radius;exit();
        $sent_cv_count = CvNote::where(['sale_id' => $id, 'status' => 'active'])->count();
        $cv_limit = CvNote::where(['sale_id' => $id, 'status' => 'active'])
            ->count();
        $job = Office::join('sales', 'offices.id', '=', 'sales.head_office')
            ->join('units', 'units.id', '=', 'sales.head_office_unit')
            ->select('sales.*', 'offices.name', 'units.contact_name',
                'units.contact_email', 'units.unit_name', 'units.contact_phone_number', 'sales.id as sale_id')
            ->where(['sales.status' => 'active', 'sales.id' => $id])->first();
        $sale_export_id= $id;
        $active_clients = [];
        if ($sent_cv_count == $job['send_cv_limit']) {

            $active_clients = Client::join('histories', function ($join) use ($id) {
                $join->on('histories.client_id', '=', 'clients.id');
                $join->where('histories.sale_id', '=', $id);
            })->whereIn('histories.sub_stage', ['quality_cvs','quality_cleared','crm_save','crm_request','crm_request_save','crm_request_confirm','crm_interview_save','crm_interview_attended','crm_prestart_save', 'crm_start_date','crm_start_date_save','crm_start_date_back','crm_invoice','crm_final_save'])
                ->where('histories.status', '=', 'active')
                ->select('clients.app_name','clients.app_postcode',
                    'histories.stage','histories.sub_stage','histories.history_added_date','histories.history_added_time')
                ->get()->toArray();
        }
//dd($active_clients);
        return view('administrator.resource.15km_applicants', compact('id', 'job', 'sent_cv_count', 'active_clients','sale_export_id','radius','cv_limit'));
    }
    public function get15kmClientsAjax($id,$radius=8)
    {
        /*** not used

         */
        // $page_url = 'clients-within-15-km/'.$id;
        $sale_id=$id;
        $job_result = Sale::find($id);
        $job_title = $job_result->job_title;
        // echo $job_title;
        $job_title_prop=null;
        if ($job_title == "nonnurse specialist"){
            $job_title_prop =  $job_result->job_title_prof;

        }
        $job_postcode = $job_result->postcode;
//        if($radius==10)
//        {
//            $radius = 8;
//
//        }
        $radius = 30;

        $postcode_para = urlencode($job_postcode).',UK';
        $apiKey = env('GOOGLE_API_KEY');

//        $apiKey = 'AIzaSyCA1maGn_da4Y35faHXfCxa4sau-bYlSKk'; // Replace with your actual API key
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode_para}&key={$apiKey}";
        $resp_json = file_get_contents($url);
        $near_by_clients = '';

        $resp = json_decode($resp_json, true);

        // print_r($resp);exit();
        if ($resp['status'] == 'OK') {
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
//dd($lati,$longi);
            $near_by_clients = $this->distance($lati, $longi, $radius, $job_title,$job_title_prop);
        } else {
            echo "<strong>ERROR: {$resp['status']}</strong>";
        }
        if($near_by_clients != null)
        {
            $non_interest_response = $this->check_not_interested_clients($near_by_clients, $id);
            $check_applicant_availibility = array_values($non_interest_response);
        }
        else
        {
            $check_applicant_availibility = array_values([]);
        }

        return datatables($check_applicant_availibility)
            ->addColumn('action', function ($applicant) use ($id) {
                $status_value = 'open';
                if ($applicant['paid_status'] == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant['cv_notes'] as $key => $value) {
                        if ($value['status'] == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif (($value['status'] == 'disable') && ($value['sale_id'] == $id)) {
                            $status_value = 'reject';
                            break;
                        } elseif ($value['status'] == 'disable') {
                            $status_value = 'reject';
                        } elseif (($value['status'] == 'paid') && ($value['sale_id'] == $id) && ($applicant['paid_status'] == 'open')) {
                            $status_value = 'paid';
                            break;
                        }
                    }
                }

                /***
                foreach ($applicant['cv_notes'] as $key => $value) {
                if ($value['status'] == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value['status'] == 'disable' && $value['sale_id'] == $id) {
                $status_value = 'reject_job';
                break;
                } elseif ($value['status'] == 'disable') {
                $status_value = 'reject';
                } elseif ($value['status'] == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                $content = "";
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class=list-icons-item" data-toggle="dropdown">
                                    <i class="bi bi-list"></i>
                                </a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($status_value == 'open' || $status_value == 'reject') {
                    $content .= '<a href="#" class="dropdown-item"
                data-controls-modal="#modal_form_horizontal' . $applicant['id'] . '"
                data-backdrop="static" data-keyboard="false" data-toggle="modal"
                data-target="#modal_form_horizontal' . $applicant['id'] . '">
                Decline Opportunity</a>';

                    $content .= '<a href="#" class="dropdown-item"
                data-controls-modal="#sent_cv' . $applicant['id'] . '" data-backdrop="static"
                data-keyboard="false" data-toggle="modal"
                data-target="#sent_cv' . $applicant['id'] . '">
                Share Resume</a>';

                    $content .= '<a href="#" class="dropdown-item"
                data-controls-modal="#call_back' . $applicant['id'] . '"
                data-backdrop="static" data-keyboard="false" data-toggle="modal"
                data-target="#call_back' . $applicant['id'] . '">Schedule Callback</a>';

                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // NOT INTERESTED MODAL
                    $content .= '<div id="modal_form_horizontal' . $applicant['id'] . '"
                                    class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Enter Reason Below:</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $url = '/mark-applicant';
                    $csrf = csrf_token();
                    $content .= '<form action="' . $url . '" method="POST" class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' . $csrf . '">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Reason</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id"
                                   value="' . $applicant['id'] . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $id . '">';
                    $content .= '<textarea name="reason" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-success bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // NOT INTERESTED MODAL END

                    // SEND CV MODAL
                    $content .= '<div id="sent_cv' . $applicant['id'] . '"
                                   class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Add Notes Below:</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $url2 = '/applicant-cv-to-quality/';
                    $csrf2 = csrf_token();
                    $content .= '<form action="' . $url2 . $applicant['id'] . '" method="GET" class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' . $csrf2 . '">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant['id'] . '">';
                    $content .= '<input type="hidden" name="sale_hidden_id" value="' . $id . '">';
                    $content .= '<textarea name="details" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-success bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // SEND CV MODAL END
                    // NO NURSING HOME MODAL
                    $content .= '<div id="no_nurse_home' . $applicant['id'] . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Add Notes :</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $url3 = '/sent-to-nurse-home';
                    $csrf3 = csrf_token();
                    $content .= '<form action="' . $url3 . '" method="GET" class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' . $csrf3 . '">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant['id'] . '">';
                    $content .= '<input type="hidden" name="sale_hidden_id" value="' . $id . '">';
                    $content .= '<textarea name="details" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-success bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // NO NURSING HOME MODAL END
                    // CALLBACK MODAL
                    $content .= '<div id="call_back' . $applicant['id'] . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Add  Notes Below:</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $url4 = '/sent-applicant-to-call-back-list';
                    $csrf4 = csrf_token();
                    $content .= '<form action="' . $url4 . '" method="GET" class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' . $csrf4 . '">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id"
                            value="' . $applicant['id'] . '">';
                    $content .= '<input type="hidden" name="sale_hidden_id" value="' . $id . '">';
                    $content .= '<textarea name="details" class="form-control" cols="30" rows="4"
                            placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-success bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // CALLBACK MODAL END
                } elseif ($status_value == 'sent' || $status_value == 'reject_job' || $status_value == 'paid') {
                    $content .= '<a href="#" class="disabled dropdown-item">Disable</a>';

                }
                return $content;
            })
            ->addColumn('status', function ($applicant) use ($id) {
                $status_value = 'open';
//                $color_class = 'bg-teal-800';
                $color_class = 'badge-primary';
                if ($applicant['paid_status'] == 'close') {
                    $status_value = 'paid';
//                    $color_class = 'bg-slate-700';
                    $color_class = 'badge-dark';
                } else {
                    foreach ($applicant['cv_notes'] as $key => $value) {
                        if ($value['status'] == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif (($value['status'] == 'disable') && ($value['sale_id'] == $id)) {
                            $status_value = 'reject_job';
                            break;
                        } elseif ($value['status'] == 'disable') {
                            $status_value = 'reject';
                        } elseif (($value['status'] == 'paid') && ($value['sale_id'] == $id) && ($applicant['paid_status'] == 'open')) {
                            $status_value = 'paid';
                            $color_class = 'bi bi-bootstrap';
                            break;
                        }
                    }
                }
                /***
                foreach ($applicant['cv_notes'] as $key => $value) {
                if ($value['status'] == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value['status'] == 'disable' && $value['sale_id'] == $id) {
                $status_value = 'reject_job';
                break;
                } elseif ($value['status'] == 'disable') {
                $status_value = 'reject';
                } elseif ($value['status'] == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */
                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('applicant_notes', function($applicant) use ($id) {

                $app_new_note = ModuleNote::where(['module_noteable_id' =>$applicant['id'], 'module_noteable_type' =>'App\Models\Applicant'])
                    ->select('module_notes.details')
                    ->orderBy('module_notes.id', 'DESC')
                    ->first();
                $app_notes_final='';
                if($app_new_note){
                    $app_notes_final = $app_new_note->details;

                }
                else{
                    $app_notes_final = $applicant['applicant_notes'];
                }
                $status_value = 'open';
                $postcode = '';
                if ($applicant['paid_status'] == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant['cv_notes'] as $key => $value) {
                        if ($value['status'] == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value['status'] == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }

                if($applicant['is_blocked'] == 0 && $status_value == 'open' || $status_value == 'reject')
                {

                    $content = '';
                    // if ($status_value == 'open' || $status_value == 'reject'){

                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant['id'].'"
                             data-controls-modal="#clear_cv'.$applicant['id'].'"
                             data-backdrop="static" data-keyboard="false" data-toggle="modal"
                             data-target="#clear_cv' . $applicant['id'] . '">"'.$app_notes_final.'"</a>';
                    $content .= '<div id="clear_cv' . $applicant['id'] . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('block_or_casual_notes') . '" method="POST" id="app_notes_form' . $applicant['id'] . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .='<div id="app_notes_alert' . $applicant['id'] . '"></div>';
                    $content .= '<div id="sent_cv_alert' . $applicant['id'] . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant['id'] . '">';
                    $content .= '<input type="hidden" name="applicant_sale_id" value="' . $id . '">';
                    $content .= '<input type="hidden" name="applicant_page' . $applicant['id'] . '" value="15_km_clients_nurses">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant['id'] .'" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<select name="reject_reason" class="form-control crm_select_reason" id="reason' . $applicant['id'] .'">';
                    $content .= '<option value="0" >Select Option</option>';
                    $content .= '<option value="1">Casual Notes</option>';
                    $content .= '<option value="2">Block Client Notes</option>';
                    $content .= '<option value="3">Temporary Not Interested clients Notes</option>';
                    $content .= '<option value="4">No Response</option>';
                    $content .= '</select>';
                    $content .= '</div>';
                    $content .= '</div>';

                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    $content .= '<button type="button" class="btn bg-dark legitRipple sent_cv_submit" data-dismiss="modal">Close</button>';

                    $content .= '<button type="submit" data-note_key="' . $applicant['id'] . '" value="cv_sent_save" class="btn btn-success bg-teal legitRipple sent_cv_submit app_notes_form_submit">Save</button>';

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
            ->addColumn("applicant_postcode", function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant['paid_status'] == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant['cv_notes'] as $key => $value) {
                        if ($value['status'] == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value['status'] == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /***
                foreach ($applicant['cv_notes'] as $key => $value) {
                if ($value['status'] == 'active') {
                $status_value = 'sent'; // alert-success
                break;
                } elseif ($value['status'] == 'disable') {
                $status_value = 'reject'; // alert-danger
                } elseif ($value['status'] == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if($status_value == 'open' || $status_value == 'reject') {
                    $postcode .= '<a href="/available-jobs/'.$applicant['id'].'">';
                    $postcode .= $applicant['app_postcode'];
                    $postcode .= '</a>';
                } else {
                    $postcode .= $applicant['app_postcode'];
                }
                return $postcode;
            })
            ->addColumn('download', function ($applicant) {
                $download = '<a href="'. route('downloadApplicantCv',$applicant['id']).'">
                       <i class="fa fa-file-download"></i>
                    </a>';
                return $download;
            })
            ->addColumn('updated_cv', function ($applicant) {
                return
                    '<a href="' . route('downloadUpdatedApplicantCv', $applicant['id']) . '">
                       <i class="fa fa-file-download" style=""></i>
                    </a>';
            })
            ->addColumn('upload', function ($applicant) {
                return
                    '<a href="#"
                data-controls-modal="#import_applicant_cv" class="import_cv"
                data-backdrop="static"
                data-keyboard="false" data-toggle="modal" data-id="'.$applicant['id'].'"
                data-target="#import_applicant_cv">
                 <i class="fas fa-file-upload text-teal-400" style="font-size: 30px;"></i>
                 &nbsp;</a>';
                // '<a href="' . route('downloadCv', $clients->id) . '">
                //    <i class="fas fa-file-upload text-teal-400" style="font-size: 30px;"></i>
                // </a>';
            })
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                // $job_title_desc = ($applicant['job_title_prof']!='')?$applicant['applicant_job_title'].' ('.$applicant['job_title_prof'].')':$applicant['applicant_job_title'];
                if($applicant['app_job_title_prof']!=null)
                {


                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant['app_job_title_prof'])->first();
                    $job_title_desc = $applicant['app_job_title'].' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant['app_job_title'];
                }
                return $job_title_desc;

            })
            ->editColumn('updated_at', function($applicant){
                $updatedAt = new Carbon($applicant['updated_at']);
                return $updatedAt->timestamp;
            })

            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant['paid_status'] == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant['paid_status'] == 'open' || $applicant['paid_status'] == 'pending' */
                    foreach ($applicant['cv_notes'] as $key => $value) {
                        if ($value['status'] == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value['status'] == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /***
                foreach ($applicant['cv_notes'] as $key => $value) {
                if ($value['status'] == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value['status'] == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value['status'] == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','applicant_postcode','download','updated_cv','upload','applicant_notes','status', 'action'])
            ->make(true);
    }
    function getAllTitles($job_title)
    {
//dd($job_title);
        $title = array();
        if ($job_title === 'rgn/rmn') {
            $title[0] = "rgn";
            $title[1] = "rmn";
            $title[2] = "rmn/rnld";
            $title[3] = "rgn/rmn/rnld";
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "rmn/rnld") {
            $title[0] = "rmn";
            $title[1] = "rnld";
            $title[2] = "rgn/rmn";
            $title[3] = "rgn/rmn/rnld";
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "rgn/rmn/rnld") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === 'rgn') {
            $title[0] = "rgn/rmn";
            $title[1] = "rgn/rmn/rnld";
            $title[2] = $job_title;
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "rmn") {
            $title[0] = "rgn/rmn";
            $title[1] = "rmn/rnld";
            $title[2] = "rgn/rmn/rnld";
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "rnld") {
            $title[0] = "rmn/rnld";
            $title[1] = "rgn/rmn/rnld";
            $title[2] = $job_title;
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "senior nurse") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = "rgn/rmn/rnld";
            $title[6] = "senior nurse";
            $title[7] = "clinical lead";
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "nurse deputy manager") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = "rgn/rmn/rnld";
            $title[6] = "senior nurse";
            $title[7] = "clinical lead";
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "nurse manager") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = "rgn/rmn/rnld";
            $title[6] = "nurse deputy manager";
            $title[7] = "senior nurse";
            $title[8] = "clinical lead";
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "clinical lead") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = "rgn/rmn/rnld";
            $title[6] = "nurse deputy manager";
            $title[7] = "senior nurse";
            $title[8] = "clinical lead";
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "rcn") {
            $title[0] = "rmn";
            $title[1] = "rgn";
            $title[2] = "rnld";
            $title[3] = "rgn/rmn";
            $title[4] = "rmn/rnld";
            $title[5] = "rgn/rmn/rnld";
            $title[6] = "nurse deputy manager";
            $title[7] = "senior nurse";
            $title[8] = "clinical lead";
            $title[9] = "rcn";
            $title[10] = $job_title;
        } elseif ($job_title === "head chef") {
            $title[0] = "sous chef";
            $title[1] = "Senior sous chef";
            $title[2] = "junior sous chef";
            $title[3] = "head chef";
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        } elseif ($job_title === "sous chef") {
            $title[0] = "chef de partie";
            $title[1] = "Senior chef de partie";
            $title[2] = "sous chef";
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        }
        elseif ($job_title === "chef de partie") {
            $title[0] = "junior chef de partie";
            $title[1] = "Demmi chef de partie";
            $title[2] = "chef de partie";
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        }
        else {
            $title[0] = $job_title;
            $title[1] = $job_title;
            $title[2] = $job_title;
            $title[3] = $job_title;
            $title[4] = $job_title;
            $title[5] = $job_title;
            $title[6] = $job_title;
            $title[7] = $job_title;
            $title[8] = $job_title;
            $title[9] = $job_title;
            $title[10] = $job_title;
        }
        return $title;
    }

    function distance($lat, $lon, $radius, $job_title,$job_title_prop=null)
    {
//dd($job_title_prop);

//        $title = $this->getAllTitles($job_title);

//dd($job_title_prop);
        $location_distance = Client::with('cv_notes')->select(DB::raw("*, ((ACOS(SIN($lat * PI() / 180) * SIN(app_lat * PI() / 180) +
                COS($lat * PI() / 180) * COS(app_lat * PI() / 180) * COS(($lon - app_long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)
                AS distance"))->having("distance", "<", $radius)->orderBy("distance")
//            ->where(array("app_status" => "active", "is_blocked" => "0")); //->get();
            ->where(array("app_status" => "active", "is_in_nurse_home" => "no", "is_blocked" => "0", 'is_callback_enable' => 'no')); //->get();
//dd($location_distance->get()->toArray());
        if ($job_title_prop!=null){

            $job_title_cate=$job_title_prop;
//            dd($job_title_cate);
            $location_distance = $location_distance->where("app_job_title_prof", $job_title_cate)->get();

        }else{


            $title = $this->getAllTitles($job_title);
            $location_distance = $location_distance->where("app_job_title",$title[0] )
                ->orWhere("app_job_title", $title[1])
                ->orWhere("app_job_title", $title[2])->orWhere("app_job_title", $title[3])
                ->orWhere("app_job_title", $title[4])->orWhere("app_job_title", $title[5])
                ->orWhere("app_job_title", $title[6])->orWhere("app_job_title", $title[7])
                ->orWhere("app_job_title", $title[8])->orWhere("app_job_title", $title[9])
                ->orWhere("app_job_title", $title[10])->get();

        }

        //$location_distance = $location_distance->where("applicant_job_title", $title1)->get();
        //$location_
        // new query
        return $location_distance;
    }

    public function getLast2MonthsBlockedApplicantAdded()
    {
        // $end_date = Carbon::now();
        // //$edate21 = $end_date->subDays(31); // 9 + 21 + excluding last_day . 00:00:00
        // $edate = $end_date->format('Y-m-d');
        // $start_date = $end_date->subMonths(60);
        // $sdate = $start_date->format('Y-m-d');
        // echo $edate.' and '.$sdate;exit();
        $interval = 60;
        return view('administrator.resource.blocked_clients', compact('interval'));
    }

    public function getLast2MonthsBlockedApplicantAddedAjax()
    {

        $end_date = Carbon::now();
        //$edate21 = $end_date->subDays(31); // 9 + 21 + excluding last_day . 00:00:00
        $edate = $end_date->format('Y-m-d');

        $start_date = $end_date->subMonths(60);
        $sdate = $start_date->format('Y-m-d');
        $result = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_email','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status')
//            ->leftJoin('clients_pivot_sales', 'clients.id', '=', 'clients_pivot_sales.applicant_id')
//            ->whereBetween('clients.updated_at', [$sdate, $edate])
            ->whereDate('clients.updated_at', '<=', $edate)
            ->where("clients.app_status", "=", "disable")
            ->where("clients.is_blocked", "=", 1);
//            ->where("clients.is_in_nurse_home", "=", "no")
            // ->where("clients.job_category", "=", "nurse")
//            ->where('clients_pivot_sales.applicant_id', '=', NULL);
        return datatables()->of($result)
            ->addColumn('applicant_postcode', function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** logic before open-appllicant-cv feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if ($status_value == 'open' || $status_value == 'reject'){
                    $postcode .= '<a href="/available-jobs/'.$applicant->id.'">';
                    $postcode .= $applicant->app_postcode;
                    $postcode .= '</a>';
                } else {
                    $postcode .= $applicant->app_postcode;
                }
                return $postcode;
            })
            ->addColumn('applicant_notes', function($applicant){

                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }



                $content = '';


                /*** Export clients Modal */



                /*** Unblock clients Modal */
                $content .= '<div id="applicant_action" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-sm">';
                $content .= '<div class="modal-content">';

                $content .= '<div class="modal-header">';
                $content .= '<h3 class="modal-title" >Unblock  aapclients</h3>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div id="applicant_unblock_alert"></div>';
                $content .= '<form action="' . route('scheduleInterview') . '" method="POST" id="applicant_unblock_form" class="form-horizontal">';
                $content .= csrf_field();
//                $content .= '<form action="#" method="POST" id="applicant_unblock_form" class="form-horizontal">';
//                $content .= csrf_field();
                // $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                // $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="from_date" id="from_date" placeholder="Select From Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                //                $content .= '<input type="text" class="form-control time_pickerrrr" id="anytime-time'.$applicant->id.'-'.$applicant->sale_id.'" name="schedule_time" placeholder="Select Schedule Time e.g., 00:00">';
                $content .= '<input type="text" class="form-control pickadate-year" name="to_date" id="to_date" placeholder="Select To Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple btn-block applicant_action_submit" data-app_sale="">Submit</button>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';



                if ($status_value == 'open' || $status_value == 'reject'){

                    $content .= $applicant->applicant_notes;

                } else {
                    $content .= $applicant->applicant_notes;
                }

                return $content;

            })
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;

            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = date_format($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'bg-teal-800';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'bg-slate-700';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** logic before open-applicant-cv feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */










                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge badge-success w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('action', function ($applicant) {
                $btn = '<a class="btn btn-danger btn-sm" style="font-weight: bold; color: #ffffff;" href="javascript:void(0);" onclick="confirmDelete(' . $applicant->id . ')">Unblock</a>';
                return $btn;
            })

            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','history','applicant_notes','updated_at','status','applicant_postcode','action'])
            ->make(true);

    }


    public function storeUnblockNotes(Request $request)
    {
        $applicant_id = $request->Input('applicant_hidden_id');
        $applicant_notes = $request->Input('details');
        // $notes_reason = $request->Input('reject_reason');
        // $updated_at = Carbon::now();

        Client::where('id', $applicant_id)
            ->update(['is_blocked' => '0','applicant_notes' => $applicant_notes]);
        // echo $applicant_id.' notes: '.$applicant_notes.' reason : '.$notes_reason.' date: '.$end_date;exit();
        // return redirect()->route('getlast2MonthsApp');[+]
        $interval = 60;
        return view('administrator.resource.blocked_clients', compact('interval'));
    }

    public function getTempNotInterestedApplicants(){
        // echo 'temp not';exit();
        $interval = 60;
        return view('administrator.resource.temp_not_interested', compact('interval'));
    }
    public function get_temp_not_interested_applicants_ajax()
    {
        $end_date = Carbon::now();
        //$edate21 = $end_date->subDays(31); // 9 + 21 + excluding last_day . 00:00:00
        $edate = $end_date->format('Y-m-d');

        $start_date = $end_date->subMonths(2);
        $sdate = $start_date->format('Y-m-d');
        $result = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status')
            ->where("clients.app_status", "=", "active")
            ->where(function ($query) {
                $query->whereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('applicants_pivot_sales')
                        ->whereColumn('clients.id', '=', 'applicants_pivot_sales.client_id')
                        ->where('applicants_pivot_sales.status', '=', 'active');
                })
                    ->orWhere("clients.temp_not_interested", "=", 1);
            })
            ->whereDate('clients.updated_at', '<=', $edate);





        return datatables()->of($result)
            ->addColumn('app_postcode', function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** logic before open-appllicant-cv feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if ($status_value == 'open' || $status_value == 'reject') {
                    $postcode = '<a href="/available-jobs/'.$applicant->id.'">'.$applicant->app_postcode.'</a>';
                } else {
                    $postcode = $applicant->app_postcode;
                }
                return $postcode;


            })
            ->addColumn('applicant_notes', function($applicant){

                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }



                $content = '';


                /*** Export Applicants Modal */
                $content .= '<div id="export_temp_not_interest_applicants" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-sm">';
                $content .= '<div class="modal-content">';

                $content .= '<div class="modal-header">';
                $content .= '<h3 class="modal-title">Export Applicants</h3>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<form action="#" method="POST" id="export_block_applicants" class="form-horizontal">';
                $content .= csrf_field();
                // $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                // $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="start_date" id="start_date" placeholder="Select From Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
//                $content .= '<input type="text" class="form-control time_pickerrrr" id="anytime-time'.$applicant->id.'-'.$applicant->sale_id.'" name="schedule_time" placeholder="Select Schedule Time e.g., 00:00">';
                $content .= '<input type="text" class="form-control pickadate-year" name="end_date" id="end_date" placeholder="Select To Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple btn-block">Submit</button>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';



                /*** Unblock Applicants Modal */




                if ($status_value == 'open' || $status_value == 'reject'){
                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                 data-controls-modal="#clear_cv'.$applicant->id.'"
                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                 data-target="#clear_cv' . $applicant->id . '">"' . $applicant->applicant_notes . '"</a>';
                    $content .= '<div id="clear_cv' . $applicant->id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('interested_notes') . '" method="POST" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '"></div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label class="col-form-label">Details</label>';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id .'" class="form-control" rows="4" placeholder="Type Here.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" value="cv_sent_save" class="btn btn-primary">Interested</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                } else {
                    $content .= $applicant->applicant_notes;
                }


                return $content;

            })
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;

            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = date_format($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'badge-warning';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'badge-success';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            $color_class = 'badge-success rounder-pill';

                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                            $color_class = 'badge-danger rounder-pill';

                        }
                    }
                }
                /*** logic before open-applicant-cv feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */










                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('action', function ($applicant) {
                $btn = '<button class="btn btn-danger rounded-pill" href="javascript:void(0);" onclick="confirmDelete(' . $applicant->id . ')">Revert</button>';
                return $btn;
            })

            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','action','applicant_notes','updated_at','status','app_postcode'])
            ->make(true);
    }
    public function store_interested_notes(Request $request)
    {
        $applicant_id = $request->Input('applicant_hidden_id');
        $applicant_notes = $request->Input('details');
        // $notes_reason = $request->Input('reject_reason');
        // $updated_at = Carbon::now();

        Client::where('id', $applicant_id)
            ->update(['temp_not_interested' => 0,'applicant_notes' => $applicant_notes]);
        $items=Applicants_pivot_sales::where('client_id',$applicant_id)->get();
        if ($items->count()!=0){
            foreach ($items as $item)
            $item->delete();
        }

        // echo $applicant_id.' notes: '.$applicant_notes.' reason : '.$notes_reason.' date: '.$end_date;exit();
        // return redirect()->route('getlast2MonthsApp');[+]
        $interval = 60;
        return view('administrator.resource.temp_not_interested', compact('interval'));
    }
    public function getNotResponseApplicants(){
        // echo 'temp not';exit();
        $interval = 60;
        return view('administrator.resource.not_response_applicants', compact('interval'));
    }
    public function get_no_response_applicants()
    {
        $end_date = Carbon::now();
        //$edate21 = $end_date->subDays(31); // 9 + 21 + excluding last_day . 00:00:00
        $edate = $end_date->format('Y-m-d');

        $start_date = $end_date->subMonths(2);
        $sdate = $start_date->format('Y-m-d');
        $result = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status')

//            ->select('applicants.id', 'applicants.updated_at', 'applicants.applicant_added_time', 'applicants.applicant_name', 'applicants.applicant_job_title','applicants.job_title_prof', 'applicants.job_category', 'applicants.applicant_postcode', 'applicants.applicant_phone','applicants.applicant_homePhone','applicants.applicant_source','applicants.applicant_notes','applicants.paid_status')
//            ->leftJoin('applicants_pivot_sales', 'applicants.id', '=', 'applicants_pivot_sales.applicant_id')
//            ->whereBetween('applicants.updated_at', [$sdate, $edate])
            ->whereDate('clients.updated_at', '<=', $edate)
            ->where("clients.app_status", "=", "active")
            ->where("clients.no_response", "=", 1);
//            ->where("applicants.is_in_nurse_home", "=", "no")
            // ->where("applicants.job_category", "=", "nurse")
//            ->where('applicants_pivot_sales.applicant_id', '=', NULL);

        return datatables()->of($result)
            // ->addColumn('applicant_postcode', function ($applicant) {
            //     $status_value = 'open';
            //     $postcode = '';
            //     if ($applicant->paid_status == 'close') {
            //         $status_value = 'paid';
            //     } else {
            //         foreach ($applicant->cv_notes as $key => $value) {
            //             if ($value->status == 'active') {
            //                 $status_value = 'sent';
            //                 break;
            //             } elseif ($value->status == 'disable') {
            //                 $status_value = 'reject';
            //             }
            //         }
            //     }
            //     /*** logic before open-appllicant-cv feature
            //     foreach ($applicant->cv_notes as $key => $value) {
            //         if ($value->status == 'active') {
            //             $status_value = 'sent';
            //             break;
            //         } elseif ($value->status == 'disable') {
            //             $status_value = 'reject';
            //         } elseif ($value->status == 'paid') {
            //             $status_value = 'paid';
            //             break;
            //         }
            //     }
            //     */
            //     if ($status_value == 'open' || $status_value == 'reject'){
            //         $postcode .= '<a href="/available-jobs/'.$applicant->id.'">';
            //         $postcode .= $applicant->applicant_postcode;
            //         $postcode .= '</a>';
            //     } else {
            //         $postcode .= $applicant->applicant_postcode;
            //     }
            //     return $postcode;
            // })
            ->addColumn('applicant_notes', function($applicant){

                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }

                $content = '';

                // Export Applicants Modal
                $content .= '<div id="export_temp_not_interest_applicants" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-sm">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h3 class="modal-title">Export Applicants</h3>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<form action="#" method="POST" id="export_no_response_applicants" class="form-horizontal">';
                $content .= csrf_field();
                // $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                // $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="start_date" id="start_date" placeholder="Select From Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="end_date" id="end_date" placeholder="Select To Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple btn-block">Submit</button>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                // Unblock Applicants Modal
                $content .= '<div id="applicant_action" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-sm">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h3 class="modal-title" >Unblock Applicants</h3>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div id="applicant_unblock_alert"></div>';
                $content .= '<form action="#" method="POST" id="applicant_no_response_form" class="form-horizontal">';
                $content .= csrf_field();
                // $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                // $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="from_date" id="from_date" placeholder="Select From Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="mb-4">';
                $content .= '<div class="input-group">';
                $content .= '<span class="input-group-prepend">';
                $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                $content .= '</span>';
                $content .= '<input type="text" class="form-control pickadate-year" name="to_date" id="to_date" placeholder="Select To Date">';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple btn-block applicant_action_submit" data-app_sale="">Submit</button>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                // Applicant Notes Modal
                if ($status_value == 'open' || $status_value == 'reject'){
                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                     data-controls-modal="#clear_cv'.$applicant->id.'"
                     data-backdrop="static" data-keyboard="false" data-toggle="modal"
                     data-target="#clear_cv' . $applicant->id . '">"'.$applicant->applicant_notes.'"</a>';
                    $content .= '<div id="clear_cv' . $applicant->id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('activeResponseNotest') . '" method="POST" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '"></div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label class="col-form-label">Details</label>';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id .'" class="form-control" rows="4" placeholder="Type Here.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" value="cv_sent_save" class="btn btn-primary">Interested</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                } else {
                    $content .= $applicant->applicant_notes;
                }

                return $content;

            })
            ->addColumn("history", function ($applicant) {
                $content = '';
                $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                                 data-controls-modal="#reject_history'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#reject_history' . $applicant->id . '">History</a>';

                $content .= '<div id="reject_history'.$applicant->id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h6 class="modal-title">Rejected History';
                $content .= '<span class="font-weight-semibold">';
                $content .=  utf8_encode($applicant->app_name);
                $content .= '</span>';
                $content .= '</h6>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body" id="applicant_rejected_history'.$applicant->id.'" style="max-height: 500px; overflow-y: auto;">';

                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                return $content;
            })
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;

            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = date_format($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'badge-info bg-teal-800';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'badge-success bg-slate-700';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** logic before open-applicant-cv feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */










                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','history','applicant_notes','updated_at','status'])
            ->make(true);
    }
    public function store_active_respnse_notes(Request $request)
    {
        $applicant_id = $request->Input('applicant_hidden_id');
        $applicant_notes = $request->Input('details');
        // $notes_reason = $request->Input('reject_reason');
        // $updated_at = Carbon::now();

        Client::where('id', $applicant_id)
            ->update(['no_response' => '0','applicant_notes' => $applicant_notes]);
        // echo $applicant_id.' notes: '.$applicant_notes.' reason : '.$notes_reason.' date: '.$end_date;exit();
        // return redirect()->route('getlast2MonthsApp');[+]
        $interval = 60;
        return view('administrator.resource.not_response_applicants', compact('interval'));
    }

    public function applicantRejectedHistory(Request $request)
    {
        $applicant_id = $request->input('applicant');

        $applicants_rejected_history = CrmNote::join('sales', 'sales.id', '=', 'crm_notes.sale_id')
            ->join('units', 'units.id', '=', 'sales.head_office_unit')
            ->select('sales.job_title', 'sales.postcode', 'sales.id', 'units.unit_name', 'crm_notes.details', 'crm_notes.moved_tab_to')
            ->whereIn('crm_notes.moved_tab_to', ['cv_sent_reject', 'request_reject', 'interview_not_attended', 'start_date_hold', 'dispute'])
            ->where('crm_notes.client_id', '=', $applicant_id)
            ->get();
//        dd($applicants_rejected_history);
        $history_modal_body = view('administrator.resource.partial.applicant_rejected_history', compact('applicants_rejected_history'))->render();
        return $history_modal_body;
//        return $applicants_rejected_history;
    }

    public function getNonNurseSales()
    {
          $value = '1';
        return view('administrator.resource.non_nurse', compact('value'));
    }
    public function getNonNursingJob(Request $request)
    {
        $user = Auth::user();
        $result='';

            $sale_notes = Sales_notes::select('sale_id','sales_notes.sale_note', DB::raw('MAX(created_at) as
            sale_created_at'))
                ->groupBy('sale_id');
            $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
                // ->joinSub($sale_notes, 'sales_notes', function ($join) {
                //     $join->on('sales.id', '=', 'sales_notes.sale_id');
                // })
                //->join('sales_notes', 'sales.id', '=', 'sales_notes.sale_id')
                ->select('sales.*', 'offices.name', 'units.contact_name',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number', DB::raw("(SELECT count(cv_notes.sale_id) from cv_notes
                WHERE cv_notes.sale_id=sales.id AND cv_notes.status='active' group by cv_notes.sale_id) as result"))
                ->where(['sales.status' => 'active', 'sales.is_on_hold' => '0', 'sales.job_category' => 'non-nurses'])
                ->whereNotIn('sales.job_title', ['nonnurse specialist'])
                ->orderBy('id', 'DESC');



        // (cv_notes.status='active' or cv_notes.status='paid')
        // $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
        //     ->join('units', 'units.id', '=', 'sales.head_office_unit')
        //     ->select('sales.*', 'offices.office_name', 'units.contact_name',
        //         'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
        //     ->where(['sales.status' => 'active', 'sales.job_category' => 'nonnurse'])->orderBy('id', 'DESC');

        $aColumns = ['sale_added_date', 'sale_added_time', 'job_title', 'office_name', 'unit_name',
            'postcode', 'job_type', 'experience', 'qualification', 'salary', 'sale_notes', 'status', 'Cv Limit'];

        $iStart = $request->get('iDisplayStart');
        $iPageSize = $request->get('iDisplayLength');

        $order = 'id';
        $sort = 'DESC';

        if ($request->get('iSortCol_0')) { //iSortingCols

            $sOrder = "ORDER BY";

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
            $postcode = "<a href=\"/clients-within-15-km/{$sRow->id}\">{$sRow->postcode}</a>";
            if ($sRow->status == 'active') {
                $status = '<h5><span class="badge w-100 badge-success">Active</span></h5>';
            } else {
                $status = '<h5><span class="badge w-100 badge-danger">Disable</span></h5>';
            }

            $action = "<div class=\"btn-group\">
            <div class=\"dropdown\">
                <a href=\"#\" class=\"list-icons-item\" data-toggle=\"dropdown\">
                    <i class=\"bi bi-list\"></i>
                </a>
                <div class=\"dropdown-menu dropdown-menu-right\">
                    <a href=\"#\" class=\"dropdown-item\"
                                               data-controls-modal=\"#manager_details{$sRow->id}\"
                                               data-backdrop=\"static\"
                                               data-keyboard=\"false\" data-toggle=\"modal\"
                                               data-target=\"#manager_details{$sRow->id}\"
                                            > Manager Details </a>
                </div>
            </div>
          </div>
          <div id=\"manager_details{$sRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                            <div class=\"modal-dialog modal-sm\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Manager Details</h5>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    </div>
                                    <div class=\"modal-body\">
                                        <ul class=\"list-group\">
                                            <li class=\"list-group-item active\"><p><b>Name: </b>{$sRow->contact_name}</p>
                                            </li>
                                            <li class=\"list-group-item\"><p><b>Email: </b>{$sRow->contact_email}</p></li>
                                            <li class=\"list-group-item\"><p><b>Phone#: </b>{$sRow->contact_phone_number}</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class=\"modal-footer\">
                                        <button type=\"button\" class=\"btn bg-teal legitRipple\" data-dismiss=\"modal\">CLOSE
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>";
            $job_title_desc='';
            if(@$sRow->job_title_prof!='')
            {
                $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
                // $job_title_desc = @$sRow->job_title.' ('.@$sRow->job_title_prof.')';
            }
            else
            {
                $job_title_desc = @$sRow->job_title;
            }
            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                @$sRow->sale_added_date,
                @$sRow->sale_added_time,
                $job_title_desc,
                @$sRow->name,
                @$sRow->unit_name,
                @$postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,
                @$sRow->result==$sRow->send_cv_limit?'<span style="color:red;">Limit Reached</span>':"<span style='color:green'>".((int)$sRow->send_cv_limit - (int)$sRow->result)." Cv's limit remaining</span>",

//                @$sRow->result==$sRow->send_cv_limit?'<span style="color:red;">Limit Reached</span>':"<span style='color:green'>".((int)$sRow->send_cv_limit - (int)$sRow->result)." Cv's limit remaining</span>",

            );


            $i++;

        }

        //  print_r($output);
        echo json_encode($output);
    }
    public function getNonNurseSpecialistSales()
    {
        $value = '1';
        return view('administrator.resource.non_nurse_specialist', compact('value'));
    }
    public function getNonNursingSpecialistJob(Request $request)
    {
        $user = Auth::user();
        $result='';

            $sale_notes = Sales_notes::select('sale_id','sales_notes.sale_note', DB::raw('MAX(created_at) as
        sale_created_at'))
                ->groupBy('sale_id');
            $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
                ->join('units', 'units.id', '=', 'sales.head_office_unit')
//                ->joinSub($sale_notes, 'sales_notes', function ($join) {
//                    $join->on('sales.id', '=', 'sales_notes.sale_id');
//                })
                //->join('sales_notes', 'sales.id', '=', 'sales_notes.sale_id')
                ->select('sales.*', 'offices.name', 'units.contact_name',
                    'units.contact_email', 'units.unit_name', 'units.contact_phone_number', DB::raw("(SELECT count(cv_notes.sale_id) from cv_notes
            WHERE cv_notes.sale_id=sales.id AND cv_notes.status='active' group by cv_notes.sale_id) as result"))
                ->where(['sales.status' => 'active', 'sales.is_on_hold' => '0', 'sales.job_category' => 'non-nurses', 'sales.job_title' => 'nonnurse specialist'])
                ->orderBy('id', 'DESC');


        // (cv_notes.status='active' or cv_notes.status='paid')
        // $result = Office::join('sales', 'offices.id', '=', 'sales.head_office')
        //     ->join('units', 'units.id', '=', 'sales.head_office_unit')
        //     ->select('sales.*', 'offices.office_name', 'units.contact_name',
        //         'units.contact_email', 'units.unit_name', 'units.contact_phone_number')
        //     ->where(['sales.status' => 'active', 'sales.job_category' => 'nonnurse'])->orderBy('id', 'DESC');

        $aColumns = ['sale_added_date', 'sale_added_time', 'job_title', 'office_name', 'unit_name',
            'postcode', 'job_type', 'experience', 'qualification', 'salary', 'sale_note', 'status', 'Cv Limit'];

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
            $postcode = "<a href=\"/clients-within-15-km/{$sRow->id}\">{$sRow->postcode}</a>";
            if ($sRow->status == 'active') {
                $status = '<h5><span class="badge w-100 badge-success">Active</span></h5>';
            } else {
                $status = '<h5><span class="badge w-100 badge-danger">Disable</span></h5>';
            }

            $action = "<div class=\"list-icons\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-toggle=\"dropdown\">
                <i class=\"icon-menu9\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">
                <a href=\"#\" class=\"dropdown-item\"
                                           data-controls-modal=\"#manager_details{$sRow->id}\"
                                           data-backdrop=\"static\"
                                           data-keyboard=\"false\" data-toggle=\"modal\"
                                           data-target=\"#manager_details{$sRow->id}\"
                                        > Manager Details </a>
            </div>
        </div>
      </div>
      <div id=\"manager_details{$sRow->id}\" class=\"modal fade\" tabindex=\"-1\">
                        <div class=\"modal-dialog modal-sm\">
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <h5 class=\"modal-title\">Manager Details</h5>
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                </div>
                                <div class=\"modal-body\">
                                    <ul class=\"list-group\">
                                        <li class=\"list-group-item active\"><p><b>Name: </b>{$sRow->contact_name}</p>
                                        </li>
                                        <li class=\"list-group-item\"><p><b>Email: </b>{$sRow->contact_email}</p></li>
                                        <li class=\"list-group-item\"><p><b>Phone#: </b>{$sRow->contact_phone_number}</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class=\"modal-footer\">
                                    <button type=\"button\" class=\"btn bg-teal legitRipple\" data-dismiss=\"modal\">CLOSE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>";
            $job_title_desc='';
            if(@$sRow->job_title_prof!='')
            {
                $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $sRow->job_title_prof)->first();
                $job_title_desc = $sRow->job_title.' ('.$job_prof_res->name.')';
//                dd($job_prof_res->specialist_prof);
                // $job_title_desc = @$sRow->job_title.' ('.@$sRow->job_title_prof.')';
            }
            else
            {
                $job_title_desc = @$sRow->job_title;
            }
            $output['aaData'][] = array(
                "DT_RowId" => "row_{$sRow->id}",
                //    @$checkbox,
                @$sRow->sale_added_date,
                @$sRow->sale_added_time,
                $job_title_desc,
                @$sRow->name,
                @$sRow->unit_name,
                @$postcode,
                @$sRow->job_type,
                @$sRow->experience,
                @$sRow->qualification,
                @$sRow->salary,
//                @$sRow->sale_note,
//                @$status,
//                @$action,
                @$sRow->result==$sRow->send_cv_limit?'<span style="color:red;">Limit Reached</span>':"<span style='color:green'>".((int)$sRow->send_cv_limit - (int)$sRow->result)." Cv's limit remaining</span>",

            );


            $i++;

        }

        //  print_r($output);
        echo json_encode($output);
    }


    function check_not_interested_clients($applicants_object, $job_id)
    {

        $pivot_result = array();
        $filter_applicant = array();
        $app_id = '';
        $job_db_id='';
        foreach ($applicants_object as $key => $value) {
            $applicant_id = $value->id;
            $pivot_result[] = Applicants_pivot_sales::where("client_id", $applicant_id)->where("sale_id", $job_id)->where('status','disable')->first();
            foreach ($pivot_result as $res)
            {
                if(isset($res['client_id']) && isset($res['client_id']))
                {
                    $app_id = $res['client_id'];
                    $job_db_id = $res['sale_id'];
                }
            }
            if (($applicant_id == $app_id) && ($job_id == $job_db_id)) {
                $applicants_object->forget($key);
            }
        }
        foreach ($applicants_object as $key => $filter_val) {
            if (($filter_val['is_in_nurse_home'] == 'yes') || ($filter_val['is_callback_enable'] == 1) || ($filter_val['is_blocked'] == 1)) {
                $applicants_object->forget($key);

            }
            unset( $filter_val['distance']);
        }
        return $applicants_object->toArray();

    }


    public function getMarkApplicant(Request $request)
    {
        date_default_timezone_set('Europe/London');
        $audit_data['applicant'] = $applicant_id = $request->input('applicant_hidden_id');
        $audit_data['action'] = "Not Interested";
        $audit_data['sale'] = $job_id = $request->input('job_hidden_id');
        $not_interested_reason_note = $request->input('reason');

        $interest = new Applicants_pivot_sales();
        $audit_data['added_date'] = $interest->interest_added_date = Carbon::now()->format("Y-m-d");
        $audit_data['added_time'] = $interest->interest_added_time = Carbon::now()->format("H:i:s");
        $interest->is_interested = "no";
        $interest->client_id = $applicant_id;
        $interest->sale_id = $job_id;
        $interest->status = 'active';
        $interest->details = $not_interested_reason_note;
        $interest->save();
        $last_inserted_interest = $interest->id;
        if ($last_inserted_interest) {
//            Carbon::now()->format("Y-m-d");
//            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
//            $notes_for_range = new Notes_for_range_applicants();
//            $notes_for_range->applicants_pivot_sales_id = $last_inserted_interest;
//            $audit_data['reason'] = $notes_for_range->reason = $not_interested_reason_note;
//            $notes_for_range->save();
//            $notes_for_range_last_insert_id = $notes_for_range->id;

            //$pivot_object = Applicants_pivot_sales::where('id',$last_inserted_interest)->get();
//            $return_response = $this->check_interest_mark_note($pivot_object);
//            if($return_response){
//                return redirect('direct-resource')->with('jobApplicantInterest', 'Job Interest Note Added');
//            }
//            else{
//                return redirect('direct-resource')->with('jobApplicantInterestFail', 'Job Interest Note Cannot be Added');
//            }
            /*** activity log
             * $action_observer = new ActionObserver();
             * $action_observer->action($audit_data, 'Resource');
             */
            toastr()->success('Job Interest Note Added');
            return Redirect::back()->with('jobApplicantInterest', 'Job Interest Note Added');
        } else {
            toastr()->error('WHOOPS!! Something went wrong');
            return Redirect::back()->with('jobApplicantInterestError', 'WHOOPS!! Something went wrong');
        }

    }


    public function getApplicantSentToCallBackList()
    {
        date_default_timezone_set('Europe/London');
        $audit_data['action'] = "Callback";
        $details = request()->details;
        $audit_data['applicant'] = $applicant_id = request()->applicant_hidden_id;

        $user = Auth::user();
        ApplicantNote::where('client_id', $applicant_id)
            ->whereIn('moved_tab_to', ['callback','revert_callback'])
            ->update(['status' => 'disable']);
        $applicant_note = new ApplicantNote();
        $applicant_note->user_id = $user->id;
        $applicant_note->client_id = $applicant_id;
        $applicant_note->added_date =  \Carbon\Carbon::now()->format("Y-m-d");
        $applicant_note->added_time = Carbon::now()->format("H:i:s");
        $audit_data['details'] = $applicant_note->details = $details;
        $applicant_note->moved_tab_to = "callback";
        $applicant_note->status = "active";
        $applicant_note->save();
        $last_inserted_note = $applicant_note->id;
        if ($last_inserted_note > 0) {
            Client::where(['id' => $applicant_id])->update(['is_callback_enable' => 1]);
            /*** activity log
             * $action_observer = new ActionObserver();
             * $action_observer->action($audit_data, 'Resource');
             */
            return Redirect::back()->with('potentialCallBackSuccess', 'Added');
        }
        return redirect()->back();
    }
    public function getLast7DaysApplicantAdded()
    {
//        last_2_months_client_added.blade.php

        $id=3;

        $interval = 7;
        return view('administrator.resource.clients.last_7_days_client_added', compact('interval','id'));
    }

    public function get7DaysApplicants()
    {
        date_default_timezone_set('Europe/London');

        $end_date = Carbon::now();
        $edate = $end_date->format('Y-m-d') . " 23:59:59";
        $start_date = $end_date->subDays(10);
        $sdate = $start_date->format('Y-m-d') . " 00:00:00";

        $result1 = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_email','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status')
            ->leftJoin('applicants_pivot_sales', 'clients.id', '=', 'applicants_pivot_sales.client_id')
            ->where("clients.app_status", "=", "active");
//                    ->where("applicants.is_no", "=", "no")
//                    ->where("applicants.job_category", "=", $category)
//        if ($id == "44"){
//            $result1= $result1->where("app_job_category", '=',"nurses");
//        }elseif ($id == "45"){
//            $result1= $result1->where("app_job_category", "=","non-nurses")->whereNotIn('applicant_job_title', ['nonnurse specialist']);
//        }elseif ($id =="46"){
//            $result1= $result1->where(["job_category" => "non-nurse", "applicant_job_title" => "nonnurse specialist" ]);
//        }elseif ($id =="47"){
//            $result1= $result1->where(["job_category" => "chef"]);
//
//        }
        $result = $result1->where("clients.is_blocked", "=", "0")
            ->where("clients.temp_not_interested", "=", "0")
            ->where('clients.is_no_job',"=","0")
            //live query  commented pivot sale table not idea
            ->where('applicants_pivot_sales.client_id', '=', NULL)
            ->whereBetween('clients.updated_at', [$sdate, $edate])
            ->orderBy('updated_at','DESC')

        ;

        return datatables()->of($result)
//            ->filter(function ($query) {
//                if (request()->has('created_at')) {
//                    $date = new DateTime(request('created_at'));
//                    $date = date_format($date, 'Y-m-d');
//                    $query->whereDate('applicants.created_at', $date);
//                }
//            })
            ->addColumn('applicant_postcode', function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** old logic before open applicant cv
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent'; // alert-success
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject'; // alert-danger
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if ($status_value == 'open' || $status_value == 'reject') {
                    $postcode .= '<a href="/available-jobs/'.$applicant->id.'">';
                    $postcode .= $applicant->app_postcode;
                    $postcode .= '</a>';
                } else {
                    $postcode .= $applicant->app_postcode;
                }
                return $postcode;
            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = ($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('applicant_notes', function($applicant){

                $app_new_note = ModuleNote::where(['module_noteable_id' =>$applicant->id, 'module_noteable_type' =>'App\Models\Applicant'])
                    ->select('module_notes.details')
                    ->orderBy('module_notes.id', 'DESC')
                    ->first();
                $app_notes_final='';
                if($app_new_note){
                    $app_notes_final = $app_new_note->details;

                }
                else{
                    $app_notes_final = $applicant->applicant_notes;
                }
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }

                if($applicant->is_blocked == 0 && $status_value == 'open' || $status_value == 'reject')
                {

                    $content = '';
                    // if ($status_value == 'open' || $status_value == 'reject'){

                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                                 data-controls-modal="#clear_cv'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#clear_cv' . $applicant->id . '">"'.$app_notes_final.'"</a>';
                    $content .= '<div id="clear_cv' . $applicant->id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('block_or_casual_notes') . '" method="POST" id="app_notes_form' . $applicant->id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .='<div id="app_notes_alert' . $applicant->id . '"></div>';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="applicant_page' . $applicant->id . '" value="7_days_applicants">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id .'" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<select name="reject_reason" class="form-control crm_select_reason" id="reason' . $applicant->id .'">';
                    $content .= '<option value="0" >Select Reason</option>';
                    $content .= '<option value="1">Casual Notes</option>';
                    $content .= '<option value="2">Block Applicant Notes</option>';
                    $content .= '<option value="3">Temporary Not Interested Applicants Notes</option>';
                    $content .= '<option value="4">No Response</option>';

                    $content .= '</select>';
                    $content .= '</div>';
                    $content .= '</div>';

                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    $content .= '<button type="button" class="btn bg-dark legitRipple sent_cv_submit" data-dismiss="modal">Close</button>';

                    $content .= '<button type="submit" data-note_key="' . $applicant->id . '" value="cv_sent_save" class="btn bg-teal legitRipple sent_cv_submit app_notes_form_submit">Save</button>';

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
                // return $content;

            })

            ->addColumn('history', function ($applicant) {
                $content = '';
                $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'";
                                 data-controls-modal="#reject_history'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#reject_history' . $applicant->id . '">History</a>';

                $content .= '<div id="reject_history'.$applicant->id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h6 class="modal-title">Rejected History - <span class="font-weight-semibold">'.$applicant->applicant_name.'</span></h6>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body" id="applicant_rejected_history'.$applicant->id.'" style="max-height: 500px; overflow-y: auto;">';

                /*** Details are fetched via ajax request */

                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;

            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'badge-pill badge-secondary';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'badge-pill badge-success';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            $color_class = 'badge-pill badge-success';
//                            $color_class = 'badge-';

                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                            $color_class = 'badge-pill badge-danger';

                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */
                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-download"></i></span>
                    </a>';
            })
            ->addColumn('updated_cv', function ($applicant) {
                return
                    '<a href="' . route('downloadUpdatedApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-upload"></i></span>
                    </a>';
            })
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
//            ->addColumn('upload', function ($applicant) {
//                return
//                    '<a href="#"
//            data-controls-modal="#import_applicant_cv" class="import_cv"
//            data-backdrop="static"
//            data-keyboard="false" data-toggle="modal" data-id="'.$applicant->id.'"
//            data-target="#import_applicant_cv">
//             <i class="fas fa-file-upload text-teal-400" style="font-size: 30px;"></i>
//             &nbsp;</a>';
//            })
            ->addColumn('upload', function ($row) {
                return '<a href="#" onclick="uploadCv(' . $row->id . ')" class="import_cv" data-controls-modal="#import_applicant_cv" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#import_applicant_cv"><span><i class="fa fa-file-download"></i></span>&nbsp;</a>';
            })
            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','updated_at','download','updated_cv','upload','applicant_notes','status','applicant_postcode', 'history'])
            ->make(true);
    }

    public function getLast21DaysApplicantAdded()
    {


        $id=3;

        $interval = 7;
        return view('administrator.resource.clients.last_21_days_client_added', compact('interval','id'));
    }
    public function get21DaysApplicants()
    {
        date_default_timezone_set('Europe/London');

        $end_date = Carbon::now();
        $edate7 = $end_date->subDays(15);
        $edate = $edate7->format('Y-m-d') . " 23:59:59";
        $start_date = $end_date->subDays(21);
        $sdate = $start_date->format('Y-m-d') . " 00:00:00";

        $result1 = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status','clients.app_email')
            ->leftJoin('applicants_pivot_sales', 'clients.id', '=', 'applicants_pivot_sales.client_id')
            ->where("clients.app_status", "=", "active");
//                    ->where("applicants.is_no", "=", "no")
//                    ->where("applicants.job_category", "=", $category)
//        if ($id == "44"){
//            $result1= $result1->where("app_job_category", '=',"nurses");
//        }elseif ($id == "45"){
//            $result1= $result1->where("app_job_category", "=","non-nurses")->whereNotIn('applicant_job_title', ['nonnurse specialist']);
//        }elseif ($id =="46"){
//            $result1= $result1->where(["job_category" => "non-nurse", "applicant_job_title" => "nonnurse specialist" ]);
//        }elseif ($id =="47"){
//            $result1= $result1->where(["job_category" => "chef"]);
//
//        }
        $result = $result1->where("clients.is_blocked", "=", "0")
            ->where("clients.temp_not_interested", "=", "0")
            ->where('clients.is_no_job',"=","0")
            //live query  commented pivot sale table not idea
            ->where('applicants_pivot_sales.client_id', '=', NULL)
            ->whereBetween('clients.updated_at', [$sdate, $edate])

            ->orderBy('updated_at','DESC')

        ;

        return datatables()->of($result)
//            ->filter(function ($query) {
//                if (request()->has('created_at')) {
//                    $date = new DateTime(request('created_at'));
//                    $date = date_format($date, 'Y-m-d');
//                    $query->whereDate('applicants.created_at', $date);
//                }
//            })
            ->addColumn('applicant_postcode', function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** old logic before open applicant cv
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent'; // alert-success
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject'; // alert-danger
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if ($status_value == 'open' || $status_value == 'reject') {
                    $postcode .= '<a href="/available-jobs/'.$applicant->id.'">';
                    $postcode .= $applicant->app_postcode;
                    $postcode .= '</a>';
                } else {
                    $postcode .= $applicant->app_postcode;
                }
                return $postcode;
            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = ($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('applicant_notes', function($applicant){

                $app_new_note = ModuleNote::where(['module_noteable_id' =>$applicant->id, 'module_noteable_type' =>'App\Models\Applicant'])
                    ->select('module_notes.details')
                    ->orderBy('module_notes.id', 'DESC')
                    ->first();
                $app_notes_final='';
                if($app_new_note){
                    $app_notes_final = $app_new_note->details;

                }
                else{
                    $app_notes_final = $applicant->applicant_notes;
                }
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }

                if($applicant->is_blocked == 0 && $status_value == 'open' || $status_value == 'reject')
                {

                    $content = '';
                    // if ($status_value == 'open' || $status_value == 'reject'){

                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                                 data-controls-modal="#clear_cv'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#clear_cv' . $applicant->id . '">"'.$app_notes_final.'"</a>';
                    $content .= '<div id="clear_cv' . $applicant->id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('block_or_casual_notes') . '" method="POST" id="app_notes_form' . $applicant->id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .='<div id="app_notes_alert' . $applicant->id . '"></div>';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="applicant_page' . $applicant->id . '" value="21_days_applicants">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id .'" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<select name="reject_reason" class="form-control crm_select_reason" id="reason' . $applicant->id .'">';
                    $content .= '<option value="0" >Select Reason</option>';
                    $content .= '<option value="1">Casual Notes</option>';
                    $content .= '<option value="2">Block Applicant Notes</option>';
                    $content .= '<option value="3">Temporary Not Interested Applicants Notes</option>';
                    $content .= '<option value="4">No Response</option>';

                    $content .= '</select>';
                    $content .= '</div>';
                    $content .= '</div>';

                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    $content .= '<button type="button" class="btn bg-dark legitRipple sent_cv_submit" data-dismiss="modal">Close</button>';

                    $content .= '<button type="submit" data-note_key="' . $applicant->id . '" value="cv_sent_save" class="btn bg-teal legitRipple sent_cv_submit app_notes_form_submit">Save</button>';

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
                // return $content;

            })

            ->addColumn('history', function ($applicant) {
                $content = '';
                $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'";
                                 data-controls-modal="#reject_history'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#reject_history' . $applicant->id . '">History</a>';

                $content .= '<div id="reject_history'.$applicant->id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h6 class="modal-title">Rejected History - <span class="font-weight-semibold">'.$applicant->applicant_name.'</span></h6>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body" id="applicant_rejected_history'.$applicant->id.'" style="max-height: 500px; overflow-y: auto;">';

                /*** Details are fetched via ajax request */

                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;

            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'badge-pill badge-info';

                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'badge-pill badge-secondary';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            $color_class = 'badge-pill badge-success';
//                            $color_class = 'badge-warning';

                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                            $color_class = 'badge-pill badge-danger';

                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */
                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-download"></i></span>
                    </a>';
            })
            ->addColumn('updated_cv', function ($applicant) {
                return
                    '<a href="' . route('downloadUpdatedApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-upload"></i></span>
                    </a>';
            })
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
//            ->addColumn('upload', function ($applicant) {
//                return
//                    '<a href="#"
//            data-controls-modal="#import_applicant_cv" class="import_cv"
//            data-backdrop="static"
//            data-keyboard="false" data-toggle="modal" data-id="'.$applicant->id.'"
//            data-target="#import_applicant_cv">
//             <i class="fas fa-file-upload text-teal-400" style="font-size: 30px;"></i>
//             &nbsp;</a>';
//            })
            ->addColumn('upload', function ($row) {
                return '<a href="#" onclick="uploadCv(' . $row->id . ')" class="import_cv" data-controls-modal="#import_applicant_cv" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#import_applicant_cv"><span><i class="fa fa-file-download"></i></span>&nbsp;</a>';
            })
            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','updated_at','download','updated_cv','upload','applicant_notes','status','applicant_postcode', 'history'])
            ->make(true);
    }

    public function getLast2MonthsApplicantAdded()
    {
//        last_2_months_client_added.blade.php

        $id=3;

        $interval = 7;
        return view('administrator.resource.clients.last_2_months_client_added', compact('interval','id'));
    }
    public function get2MonthsApplicants()
    {
        date_default_timezone_set('Europe/London');

        $end_date = Carbon::now();
        $edate21 = $end_date->subMonth(1)->subDays(6); // 16 + 21 + excluding last_day . 00:00:00
        $edate = $edate21->format('Y-m-d');
        $start_date = $end_date->subMonths(60);
        $sdate = $start_date->format('Y-m-d');

        $result1 = Client::with('cv_notes')
            ->select('clients.id', 'clients.updated_at', 'clients.applicant_added_time', 'clients.app_name', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_postcode', 'clients.app_phone','clients.app_phoneHome','clients.app_source','clients.applicant_notes','clients.paid_status','clients.app_email')
            ->leftJoin('applicants_pivot_sales', 'clients.id', '=', 'applicants_pivot_sales.client_id')
            ->where("clients.app_status", "=", "active");
//                    ->where("applicants.is_no", "=", "no")
//                    ->where("applicants.job_category", "=", $category)
//        if ($id == "44"){
//            $result1= $result1->where("app_job_category", '=',"nurses");
//        }elseif ($id == "45"){
//            $result1= $result1->where("app_job_category", "=","non-nurses")->whereNotIn('applicant_job_title', ['nonnurse specialist']);
//        }elseif ($id =="46"){
//            $result1= $result1->where(["job_category" => "non-nurse", "applicant_job_title" => "nonnurse specialist" ]);
//        }elseif ($id =="47"){
//            $result1= $result1->where(["job_category" => "chef"]);
//
//        }
        $result = $result1->where("clients.is_blocked", "=", "0")
            ->where("clients.temp_not_interested", "=", "0")
            ->where('clients.is_no_job',"=","0")
            //live query  commented pivot sale table not idea
            ->where('applicants_pivot_sales.client_id', '=', NULL)
//            ->whereBetween('clients.updated_at', [$sdate, $edate])
            ->whereDate('clients.updated_at', '<=', $edate)

            ->orderBy('updated_at','DESC')

        ;

        return datatables()->of($result)
//            ->filter(function ($query) {
//                if (request()->has('created_at')) {
//                    $date = new DateTime(request('created_at'));
//                    $date = date_format($date, 'Y-m-d');
//                    $query->whereDate('applicants.created_at', $date);
//                }
//            })
            ->addColumn('applicant_postcode', function ($applicant) {
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }
                /*** old logic before open applicant cv
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent'; // alert-success
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject'; // alert-danger
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                break;
                }
                }
                 */
                if ($status_value == 'open' || $status_value == 'reject') {
                    $postcode .= '<a href="/available-jobs/'.$applicant->id.'">';
                    $postcode .= $applicant->app_postcode;
                    $postcode .= '</a>';
                } else {
                    $postcode .= $applicant->app_postcode;
                }
                return $postcode;
            })
            ->addColumn("updated_at",function($applicant){
                $updated_at = Carbon::parse($applicant->updated_at)->format('d F Y');
//                $date = ($updated_at,'d F Y');
                return $updated_at;
            })
            ->addColumn('applicant_notes', function($applicant){

                $app_new_note = ModuleNote::where(['module_noteable_id' =>$applicant->id, 'module_noteable_type' =>'App\Models\Applicant'])
                    ->select('module_notes.details')
                    ->orderBy('module_notes.id', 'DESC')
                    ->first();
                $app_notes_final='';
                if($app_new_note){
                    $app_notes_final = $app_new_note->details;

                }
                else{
                    $app_notes_final = $applicant->applicant_notes;
                }
                $status_value = 'open';
                $postcode = '';
                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                        }
                    }
                }

                if($applicant->is_blocked == 0 && $status_value == 'open' || $status_value == 'reject')
                {

                    $content = '';
                    // if ($status_value == 'open' || $status_value == 'reject'){

                    $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'"
                                 data-controls-modal="#clear_cv'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#clear_cv' . $applicant->id . '">"'.$app_notes_final.'"</a>';
                    $content .= '<div id="clear_cv' . $applicant->id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('block_or_casual_notes') . '" method="POST" id="app_notes_form' . $applicant->id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .='<div id="app_notes_alert' . $applicant->id . '"></div>';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="applicant_page' . $applicant->id . '" value="2_months_applicants">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id .'" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<select name="reject_reason" class="form-control crm_select_reason" id="reason' . $applicant->id .'">';
                    $content .= '<option value="0" >Select Reason</option>';
                    $content .= '<option value="1">Casual Notes</option>';
                    $content .= '<option value="2">Block Applicant Notes</option>';
                    $content .= '<option value="3">Temporary Not Interested Applicants Notes</option>';
                    $content .= '<option value="4">No Response</option>';

                    $content .= '</select>';
                    $content .= '</div>';
                    $content .= '</div>';

                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    $content .= '<button type="button" class="btn bg-dark legitRipple sent_cv_submit" data-dismiss="modal">Close</button>';

                    $content .= '<button type="submit" data-note_key="' . $applicant->id . '" value="cv_sent_save" class="btn bg-teal legitRipple sent_cv_submit app_notes_form_submit">Save</button>';

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
                // return $content;

            })

            ->addColumn('history', function ($applicant) {
                $content = '';
                $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'";
                                 data-controls-modal="#reject_history'.$applicant->id.'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#reject_history' . $applicant->id . '">History</a>';

                $content .= '<div id="reject_history'.$applicant->id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h6 class="modal-title">Rejected History - <span class="font-weight-semibold">'.$applicant->applicant_name.'</span></h6>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body" id="applicant_rejected_history'.$applicant->id.'" style="max-height: 500px; overflow-y: auto;">';

                /*** Details are fetched via ajax request */

                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;

            })
            ->addColumn('status', function ($applicant) {
                $status_value = 'open';
                $color_class = 'badge-secondary';

                if ($applicant->paid_status == 'close') {
                    $status_value = 'paid';
                    $color_class = 'badge-pill badge-success';
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $status_value = 'sent';
                            $color_class = 'badge-pill badge-success';
//                            $color_class = 'badge-warning';

                            break;
                        } elseif ($value->status == 'disable') {
                            $status_value = 'reject';
                            $color_class = 'badge-pillbadge-danger';

                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $status_value = 'sent';
                break;
                } elseif ($value->status == 'disable') {
                $status_value = 'reject';
                } elseif ($value->status == 'paid') {
                $status_value = 'paid';
                $color_class = 'bg-slate-700';
                break;
                }
                }
                 */
                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge w-100 '.$color_class.'">';
                $status .= strtoupper($status_value);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->addColumn('download', function ($applicant) {
                return
                    '<a href="' . route('downloadApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-download"></i></span>
                    </a>';
            })
            ->addColumn('updated_cv', function ($applicant) {
                return
                    '<a href="' . route('downloadUpdatedApplicantCv', $applicant->id) . '">
                       <span><i class="fa fa-file-upload"></i></span>
                    </a>';
            })
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
//            ->addColumn('upload', function ($applicant) {
//                return
//                    '<a href="#"
//            data-controls-modal="#import_applicant_cv" class="import_cv"
//            data-backdrop="static"
//            data-keyboard="false" data-toggle="modal" data-id="'.$applicant->id.'"
//            data-target="#import_applicant_cv">
//             <i class="fas fa-file-upload text-teal-400" style="font-size: 30px;"></i>
//             &nbsp;</a>';
//            })
            ->addColumn('upload', function ($row) {
                return '<a href="#" onclick="uploadCv(' . $row->id . ')" class="import_cv" data-controls-modal="#import_applicant_cv" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#import_applicant_cv"><span><i class="fa fa-file-download"></i></span>&nbsp;</a>';
            })
            ->setRowClass(function ($applicant) {
                $row_class = '';
                if ($applicant->paid_status == 'close') {
                    $row_class = 'class_dark';
                } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            $row_class = 'class_success';
                            break;
                        } elseif ($value->status == 'disable') {
                            $row_class = 'class_danger';
                        }
                    }
                }
                /*** logic before open-applicant-cv-feature
                foreach ($applicant->cv_notes as $key => $value) {
                if ($value->status == 'active') {
                $row_class = 'class_success'; // status: sent
                break;
                } elseif ($value->status == 'disable') {
                $row_class = 'class_danger'; // status: reject
                } elseif ($value->status == 'paid') {
                $row_class = 'class_dark';
                break;
                }
                }
                 */
                return $row_class;
            })
            ->rawColumns(['applicant_job_title','updated_at','download','updated_cv','upload','applicant_notes','status','applicant_postcode', 'history'])
            ->make(true);
    }

    public function getActive15kmApplicants($id)
    {

        $applicant = Client::find($id);
        return view('administrator.resource.15km_jobs', compact('applicant'));
    }
    public function get15kmJobsAvailableAjax($applicant_id){
        $applicant = Client::with('cv_notes')->find($applicant_id);
        $applicant_job_title = $applicant->app_job_title;
        $applicant_postcode = $applicant->app_postcode;
        $radius = 30;
        $postcode_para = urlencode($applicant_postcode);
        $postcode_api = env('GOOGLE_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode_para}&key={$postcode_api}";
        $resp_json = file_get_contents($url);
        $near_by_jobs = '';
        $resp = json_decode($resp_json, true);
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
            $near_by_jobs = $this->job_distance($lati, $longi, $radius, $applicant_job_title);

        } else {
            echo "<strong>ERROR: {$resp['status']}</strong>";
        }

        $jobs = $this->check_not_interested_in_jobs($near_by_jobs, $applicant_id);
        foreach ($jobs as &$job) {
            $office_id = $job['head_office'];
            $unit_id = $job['head_office_unit'];
            $office = Office::select("name")->where(["id" => $office_id, "status" => "active"])->first();
            $office = $office->name;
            $unit = Unit::select("unit_name")->where(["id" => $unit_id, "status" => "active"])->first();
            $unit = $unit->unit_name;
            $job['office_name'] = $office;
            $job['unit_name'] = $unit;
            $job['cv_notes_count']=$job['cv_notes_count'];
        }

        return datatables($jobs)
            ->editColumn('job_title',function($job){
                // $job_title_desc = ($job['job_title_prof']!='')?$job['job_title'].' ('.$job['job_title_prof'].')':$job['job_title'];
                // return $job_title_desc;

                $job_title_desc='';
                if($job['job_title_prof']!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $job['job_title_prof'])->first();
                    $job_title_desc = $job['job_title'].' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $job['job_title'];
                }
                return $job_title_desc;

            })
            ->addColumn('action', function ($job) use ($applicant) {
                $option = 'open';
                foreach ($applicant->cv_notes as $key => $value) {
                    if ($value->sale_id == $job['id']) {
                        if ($value->status == 'active') {
                            $option = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $option = 'reject';
                            break;
                        } elseif ($value->status == 'paid') {
                            $option = 'paid';
                            break;
                        }
                    }

                }
//                dd($option);
                $content = "";
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class=list-icons-item" data-toggle="dropdown">
                            <i class="bi bi-list"></i>
                        </a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if($option == 'open'|| $option == 'reject') {

                    $content .= '<a href="#" class="dropdown-item"
                                       data-controls-modal="#modal_form_horizontal'.$job['id'].'"
                                       data-backdrop="static"
                                       data-keyboard="false" data-toggle="modal"
                                       data-target="#modal_form_horizontal'.$job['id'].'">Decline Opportunity</a>';
                    $content .= '<a href="#" class="dropdown-item"
                                       data-controls-modal="#sent_cv'.$job['id'].'" data-backdrop="static"
                                       data-keyboard="false" data-toggle="modal"
                                       data-target="#sent_cv'.$job['id'].'">Share CV</a>';
//                    if ($applicant->is_in_nurse_home == 0) {
//                        $content .= '<a href="#"
//                                       class="dropdown-item"
//                                       data-controls-modal="#no_nurse_home' . $applicant['id'] . '" data-backdrop="static"
//                                       data-keyboard="false" data-toggle="modal"
//                                       data-target="#no_nurse_home' . $applicant['id'] . '">NO NURSING HOME</a>';
//                    }
                    if ($applicant->is_callback_enable == 0) {
                        $content .= '<a href="#" class="dropdown-item"
                                   data-controls-modal="#call_back' . $applicant['id'] . '" data-backdrop="static"
                                   data-keyboard="false" data-toggle="modal"
                                   data-target="#call_back' . $applicant['id'] . '">Schedule Callback</a>';
                    }
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    if ($applicant->is_in_nurse_home == 0) {
                        // No Nursing Home Modal
                        $content .= '<div id="no_nurse_home' . $applicant['id'] . '" class="modal fade" tabindex="-1">';
                        $content .= '<div class="modal-dialog modal-lg">';
                        $content .= '<div class="modal-content">';
                        $content .= '<div class="modal-header">';
                        $content .= '<h5 class="modal-title">Add Note Below:</h5>';
                        $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                        $content .= '</div>';
                        $sent_to_nurse_home_url = '/sent-to-nurse-home';
                        $sent_to_nurse_home_csrf = csrf_token();
                        $content .= '<form action="' . $sent_to_nurse_home_url . '" method="GET"
                                      class="form-horizontal">';
                        $content .= '<input type="hidden" name="_token" value="' . $sent_to_nurse_home_csrf . '">';
                        $content .= '<div class="modal-body">';
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Details</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<input type="hidden" name="applicant_hidden_id"
                             value="' . $applicant['id'] . '">';
                        $content .= '<input type="hidden" name="sale_hidden_id" value="' . $job['id'] . '">';
                        $content .= '<textarea name="details" class="form-control" cols="30" rows="4"
                            placeholder="TYPE HERE.." required></textarea>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '<div class="modal-footer">';
                        $content .= '<button type="button" class="btn btn-danger btn-link legitRipple" data-dismiss="modal">Close</button>';
                        $content .= '<button type="submit" class="btn btn-success bg-teal legitRipple">Save</button>';
                        $content .= '</div>';
                        $content .= '</form>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '</div>';
                        // /No Nursing Home Modal
                    }
                    if ($applicant->is_callback_enable == 0) {
                        // CallBack Modal
                        $content .= '<div id="call_back' . $applicant['id'] . '" class="modal fade"  tabindex="-1">';
                        $content .= '<div class="modal-dialog modal-lg">';
                        $content .= '<div class="modal-content">';
                        $content .= '<div class="modal-header">';
                        $content .= '<h5 class="modal-title">Add Notes Below:</h5>';
                        $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                        $content .= '</div>';
                        $call_back_list_url = '/sent-applicant-to-call-back-list';
                        $call_back_list_csrf = csrf_token();
                        $content .= '<form action="' . $call_back_list_url . '" method="GET"
                                  class="form-horizontal">';
                        $content .= '<input type="hidden" name="_token" value="' . $call_back_list_csrf . '">';
                        $content .= '<div class="modal-body">';
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Details</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<input type="hidden" name="applicant_hidden_id"
                        value="' . $applicant['id'] . '">';
                        $content .= '<input type="hidden" name="sale_hidden_id" value="' . $job['id'] . '">';
                        $content .= '<textarea name="details" class="form-control" cols="30" rows="4"
                         placeholder="TYPE HERE.." required></textarea>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '<div class="modal-footer">';
                        $content .= '<button type="button" class="btn btn-danger btn-link legitRipple" data-dismiss="modal">Close</button>';
                        $content .= '<button type="submit" class="btn btn-success  bg-teal legitRipple">Save</button>';
                        $content .= '</div>';
                        $content .= '</form>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '</div>';
                        // /CallBack Modal
                    }
                    // Send CV Modal
                    $sent_cv_count = CvNote::where(['sale_id' => $job['id'], 'status' => 'active'])->count();
                    $content .= '<div id="sent_cv'.$job['id'].'" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Add CV Notes Below:</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $cv_url = '/applicant-cv-to-quality';
                    $cv_csrf = csrf_token();
                    $content .= '<form action="'.$cv_url.'/'.$applicant->id.'" method="GET"
                                      class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' .$cv_csrf.'">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Sent CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$job['send_cv_limit'].'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id"
                             value="'.$applicant->id.'">';
                    $content .= '<input type="hidden" name="sale_hidden_id"
                             value="'.$job['id'].'">';
                    $content .= '<textarea name="details" class="form-control" cols="30" rows="4"
                             placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple"
                            data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // /Sent CV Modal

                    // Add To Non Interest List Modal
                    $content .= '<div id="modal_form_horizontal'.$job['id'].'" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Enter Interest Reason Below:</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $mark_url = '/mark-applicant';
                    $mark_csrf = csrf_token();
                    $content .= '<form action="'.$mark_url.'" method="POST"
                                      class="form-horizontal">';
                    $content .= '<input type="hidden" name="_token" value="' .$mark_csrf.'">';
                    $content .= '<div class="modal-body">';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Reason</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="applicant_hidden_id"
                             value="'.$applicant->id.'">';
                    $content .= '<input type="hidden" name="job_hidden_id"
                             value="'.$job['id'].'">';
                    $content .= '<textarea name="reason" class="form-control" cols="30" rows="4"
                             placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-link legitRipple"
                             data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn bg-teal legitRipple">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    // /Add To Non Interest List Modal
                } else if($option == 'sent' || $option == 'reject_job' || $option == 'paid'){
                    $content .= '<a href="#" class="disabled dropdown-item"> Disable</a>';
                }
                return $content;
            })
            ->addColumn('head_office',function($job){
                return $job['office_name'];
            })
            ->addColumn('head_office_unit',function($job){
                return $job['unit_name'];
            })
            ->editColumn('updated_at', function($job){
                $updatedAt = new Carbon($job['updated_at']);
                return $updatedAt->timestamp;
            })
            ->addColumn('status', function ($job) use($applicant){
                $value_data = 'open';
                foreach ($applicant->cv_notes as $key => $value) {
                    if ($value->sale_id == $job['id']) {
                        if ($value->status == 'active') {

                            $value_data = 'sent';
                            break;
                        } elseif ($value->status == 'disable') {
                            $value_data = 'reject_job';
                            break;
                        } elseif ($value->status == 'paid') {
                            $value_data = 'paid';
                            break;
                        }
                    }
                }
                $status = '';
                $status .= '<h3>';
                $status .= '<span class="badge badge-success badge-pill bg-teal-800">';
                $status .= strtoupper($value_data);
                $status .= '</span>';
                $status .= '</h3>';
                return $status;
            })
            ->editColumn('cv_limit',function($job){
                if($job['cv_notes_count']==null)
                {
                    $job['cv_notes_count']=0;
                }
                return $job['cv_notes_count']==$job['send_cv_limit']?'<span class="badge w-100 badge-danger" style="font-size:90%">Limit Reached</span>':"<span class='badge w-100 badge-success' style='font-size:90%'>".((int)$job['send_cv_limit'] - (int)$job['cv_notes_count'])." Cv's limit remaining</span>";
            })
            ->rawColumns(['job_title','head_office','head_office_unit','status','cv_limit','action'])
            ->make(true);
    }
    function job_distance($lat, $lon, $radius, $applicant_job_title)
    {
        $title = $this->getAllTitles($applicant_job_title);

        $location_distance = Sale::select(DB::raw("*, ((ACOS(SIN($lat * PI() / 180) * SIN(lat * PI() / 180) +
                COS($lat * PI() / 180) * COS(lat * PI() / 180) * COS(($lon - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)
                AS distance"),DB::raw("(SELECT count(cv_notes.sale_id) from cv_notes
                WHERE cv_notes.sale_id=sales.id AND cv_notes.status='active' group by cv_notes.sale_id) as cv_notes_count"))->having("distance", "<", $radius)->orderBy("distance")->where("status", "active")->where("is_on_hold", "0");
//dd($title[0],$title[3]);
//        dd($location_distance->count());
//        $location_distance = $location_distance->where("job_title", $title[0])->orWhere("job_title", $title[1])->orWhere("job_title", $title[2])->orWhere("job_title", $title[3])->orWhere("job_title", $title[4])->orWhere("job_title", $title[5])->orWhere("job_title", $title[6])->orWhere("job_title", $title[7])->get();
        $location_distance = $location_distance->where(function ($query) use ($title) {
            $query->orWhere("job_title", $title[0]);
            $query->orWhere("job_title", $title[1]);
            $query->orWhere("job_title", $title[2]);
            $query->orWhere("job_title", $title[3]);
            $query->orWhere("job_title", $title[4]);
            $query->orWhere("job_title", $title[5]);
            $query->orWhere("job_title", $title[6]);
            $query->orWhere("job_title", $title[7]);
            $query->orWhere("job_title", $title[8]);
            $query->orWhere("job_title", $title[9]);
            $query->orWhere("job_title", $title[10]);
        })->get();
        return $location_distance;
    }

    function check_not_interested_in_jobs($job_object, $applicant_id)
    {
        $pivot_result = array();
        $app_id = '';
        foreach ($job_object as $key => $value) {
            $job_id = $value->id;
            $pivot_result = Applicants_pivot_sales::where("client_id", $applicant_id)->where("sale_id", $job_id)->first();
            if (!empty($pivot_result)) {
                $job_object->forget($key);
            }

            /***
            $pivot_result[] = Applicants_pivot_sales::where("applicant_id", $applicant_id)->where("sales_id", $job_id)->first();
            foreach ($pivot_result as $res) {
            $app_id = $res['applicant_id'];
            $job_db_id = $res['sales_id'];
            }
            if (($applicant_id == $app_id) && ($job_id == $job_db_id)) {
            $job_object->forget($key);
            }
             */
        }
        return $job_object->toArray();
    }
    public function get15kmAvailableJobs($id)
    {
        $applicant = Client::find($id);
        $is_applicant_in_quality = $applicant->is_cv_in_quality;
//        if ($applicant->paid_status == 'close') {
//            // echo 'if';exit();
//            return view('administrator.resource.15km_jobs_for_closed_applicant', compact('applicant', 'is_applicant_in_quality'));
//        }
        // echo 'else';exit();
        return view('administrator.resource.15km_jobs', compact('applicant', 'is_applicant_in_quality'));
    }

    public function potentialCallBackApplicants()
    {
        return view('administrator.resource.callback_client');
    }
    public function getPotentialCallBackApplicants()
    {
        $auth_user = Auth::user();
        $callBackApplicants = Client::with('cv_notes')
            ->join('applicant_notes', 'applicant_notes.client_id', '=', 'clients.id')
            ->select("clients.id", "clients.app_job_title","clients.app_job_title_prof", "clients.app_name", "clients.app_postcode",
                "clients.app_phone", "clients.app_phoneHome", "clients.app_job_category", "clients.app_source", "clients.paid_status",
                "applicant_notes.details", "applicant_notes.added_date", "applicant_notes.added_time")
            ->where([
                'clients.app_status' => 'active', "clients.is_callback_enable" => 1,
                'applicant_notes.moved_tab_to' => 'callback','applicant_notes.status' => 'active','clients.is_no_job' => '0'
            ])->orderBy('applicant_notes.id', 'DESC');
        $raw_columns = ['applicant_job_title','history','postcode'];
        $datatable = datatables()->of($callBackApplicants)
            ->editColumn('applicant_job_title', function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;

            })

            ->addColumn('postcode', function ($applicant) {
                if ($applicant->paid_status == 'close') {
                    return $applicant->applicant_postcode;
                } else {
                    foreach ($applicant->cv_notes as $key => $value) {
                        if ($value->status == 'active') {
                            return $applicant->app_postcode;
                        }
                    }
                    return '<a href="/available-jobs/'.$applicant->id.'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
                }
            })
            ->addColumn('history', function ($applicant) {
                $content = '';
                $content .= '<a href="#" class="reject_history" data-applicant="'.$applicant->id.'";
                                 data-controls-modal="#reject_history'.$applicant['id'].'"
                                 data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                 data-target="#reject_history' . $applicant->id . '">History</a>';

                $content .= '<div id="reject_history'.$applicant->id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h6 class="modal-title">Rejected History - <span class="font-weight-semibold">'.$applicant['applicant_name'].'</span></h6>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body" id="applicant_rejected_history'.$applicant->id.'" style="max-height: 500px; overflow-y: auto;">';

                /*** Details are fetched via ajax request */

                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            });
        if ($auth_user->hasPermissionTo('resource_Potential-Callback_revert-callback')) {
            $datatable = $datatable->addColumn('checkbox', function ($applicant) {
                return '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                             <input type="checkbox" class="checkbox-index" value="'.$applicant->id.'">
                             <span></span>
                          </label>';
            })
                ->addColumn('action', function ($applicant) {
                    return '<a href="#"
               class="btn rounded-pill bg-teal legitRipple"
               data-controls-modal="#revert_call_back'.$applicant->id.'" data-backdrop="static"
               data-keyboard="false" data-toggle="modal"
               data-target="#revert_call_back'.$applicant->id.'">Revert
            </a>
            <div id="revert_call_back'.$applicant->id.'" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add Callback Notes Below:</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <form action="'.route('revertCallBackApplicants').'" method="GET" class="form-horizontal">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                            <div class="modal-body">
                                <input type="hidden" name="applicant_hidden_id" value="'.$applicant->id.'">
                                <div class="form-group">
                                    <label class="col-form-label">Details</label>
                                    <textarea name="details" class="form-control" cols="30" rows="4" placeholder="Type Here.." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>';
                });
            $raw_columns = ['history','postcode','checkbox','action'];
        }
        return $datatable->setRowClass(function ($applicant) {
            $row_class = '';
            if ($applicant->paid_status == 'close') {
                $row_class = 'class_dark';
            } else { /*** $applicant->paid_status == 'open' || $applicant->paid_status == 'pending' */
                foreach ($applicant->cv_notes as $key => $value) {
                    if ($value->status == 'active') {
                        $row_class = 'class_success'; // status: sent
                        break;
                    } elseif ($value->status == 'disable') {
                        $row_class = 'class_danger'; // status: reject
                    }
                }
            }
            return $row_class;
        })->rawColumns($raw_columns)->make(true);
    }
    public function getApplicantRevertToSearchList()
    {
        date_default_timezone_set('Europe/London');
        $audit_data['action'] = "Revert Callback";
        $details = request()->details;
        $audit_data['applicant'] = $applicant_id = request()->applicant_hidden_id;
        $user = Auth::user();
        Client::where('id',$applicant_id)->where('is_callback_enable',1)->update([
            'is_callback_enable'=>0
        ]);
        ApplicantNote::where('client_id', $applicant_id)
            ->whereIn('moved_tab_to', ['callback','revert_callback'])
            ->update(['status' => 'disable']);
        $applicant_note = new ApplicantNote();
        $applicant_note->user_id = $user->id;
        $applicant_note->client_id = $applicant_id;
        $audit_data['added_date'] = $applicant_note->added_date =Carbon::now()->format("Y-m-d");
        $audit_data['added_time'] = $applicant_note->added_time = Carbon::now()->format("H:i:s");
        $audit_data['details'] = $applicant_note->details = $details;
        $applicant_note->moved_tab_to = "revert_callback";
        $applicant_note->status = "active";
        $applicant_note->save();
        $last_inserted_note = $applicant_note->id;
        if ($last_inserted_note > 0) {
            /*** activity log
             * $action_observer = new ActionObserver();
             * $action_observer->action($audit_data, 'Resource');
             */
            return Redirect::back()->with('potentialCallBackSuccess', 'Added');
        }
        return redirect()->back();
    }


}
