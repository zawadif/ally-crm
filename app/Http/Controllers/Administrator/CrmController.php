<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Crm_rejected_cv;
use App\Models\CrmNote;
use App\Models\CvNote;
use App\Models\History;
use App\Models\Interview;
use App\Models\Office;
use App\Models\QualityNote;
use App\Models\Sale;
use App\Models\Specialist_job_titles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*** CRM - Sent CVs */
        $this->middleware('permission:CRM_Sent-CVs_list|CRM_Sent-CVs_request|CRM_Sent-CVs_save|CRM_Sent-CVs_reject|CRM_Rejected-CV_list|CRM_Request_list|CRM_Rejected-By-Request_list|CRM_Confirmation_list|CRM_Attended_list|CRM_Not-Attended_list|CRM_Start-Date_list|CRM_Start-Date-Hold_list|CRM_Invoice_list|CRM_Dispute_list|CRM_Paid_list', ['only' => ['index','crmSentCv']]);
        $this->middleware('permission:CRM_Sent-CVs_request|CRM_Sent-CVs_save|CRM_Sent-CVs_reject', ['only' => ['sentCvAction']]);
        /*** CRM - Rejected CV */
        $this->middleware('permission:CRM_Rejected-CV_list|CRM_Rejected-CV_revert-sent-cv', ['only' => ['crmRejectCv']]);
        $this->middleware('permission:CRM_Rejected-CV_revert-sent-cv', ['only' => ['revertSentCvAction']]);
        /*** CRM - Request */
        $this->middleware('permission:CRM_Request_list|CRM_Request_reject|CRM_Request_confirm|CRM_Request_save|CRM_Request_schedule-interview', ['only' => ['crmRequest']]);
        $this->middleware('permission:CRM_Request_reject|CRM_Request_confirm|CRM_Request_save', ['only' => ['requestAction']]);
        $this->middleware('permission:CRM_Request_schedule-interview', ['only' => ['getInterviewSchedule']]);
        /*** CRM - Rejected By Request */
        $this->middleware('permission:CRM_Rejected-By-Request_list|CRM_Rejected-By-Request_revert-sent-cv|CRM_Rejected-By-Request_revert-request', ['only' => ['crmRejectByRequest']]);
        $this->middleware('permission:CRM_Rejected-By-Request_revert-sent-cv|CRM_Rejected-By-Request_revert-request', ['only' => ['rejectByRequestAction']]);
        /*** CRM - Confirmation */
        $this->middleware('permission:CRM_Confirmation_list|CRM_Confirmation_revert-request|CRM_Confirmation_not-attended|CRM_Confirmation_attend|CRM_Confirmation_rebook|CRM_Confirmation_save', ['only' => ['crmConfirmation']]);
        $this->middleware('permission:CRM_Confirmation_revert-request|CRM_Confirmation_not-attended|CRM_Confirmation_attend|CRM_Confirmation_rebook|CRM_Confirmation_save', ['only' => ['afterInterviewAction']]);
        /*** CRM - Rebook */
        $this->middleware('permission:CRM_Rebook_list|CRM_Rebook_not-attended|CRM_Rebook_attend|CRM_Rebook_save', ['only' => ['crmRebook']]);
        $this->middleware('permission:CRM_Rebook_not-attended|CRM_Rebook_attend|CRM_Rebook_save', ['only' => ['rebookAction']]);
        /*** CRM - Pre-Start Date (Attend) */
        $this->middleware('permission:CRM_Attended_list|CRM_Attended_start-date|CRM_Attended_save', ['only' => ['crmPreStartDate']]);
        $this->middleware('permission:CRM_Attended_start-date|CRM_Attended_save', ['only' => ['attendedToPreStartAction']]);
        /*** CRM - Not Attended */
        $this->middleware('permission:CRM_Not-Attended_list|CRM_Not-Attended_revert-to-attended', ['only' => ['crmNotAttended']]);
        $this->middleware('permission:CRM_Not-Attended_revert-to-attended', ['only' => ['notAttendedAction']]);
        /*** CRM - Start Date */
        $this->middleware('permission:CRM_Start-Date_list|CRM_Start-Date_invoice|CRM_Start-Date_start-date-hold|CRM_Start-Date_save', ['only' => ['crmStartDate']]);
        $this->middleware('permission:CRM_Start-Date_invoice|CRM_Start-Date_start-date-hold|CRM_Start-Date_save', ['only' => ['startDateAction']]);
        /*** CRM - Start Date Hold */
        $this->middleware('permission:CRM_Start-Date-Hold_list|CRM_Start-Date-Hold_revert-start-date|CRM_Start-Date-Hold_save', ['only' => ['crmStartDateHold']]);
        $this->middleware('permission:CRM_Start-Date-Hold_revert-start-date|CRM_Start-Date-Hold_save', ['only' => ['startDateHoldAction']]);
        /*** CRM - Invoice */
        $this->middleware('permission:CRM_Invoice_list|CRM_Invoice_paid|CRM_Invoice_dispute|CRM_Invoice_save', ['only' => ['crmInvoice']]);
        $this->middleware('permission:CRM_Invoice_paid|CRM_Invoice_dispute|CRM_Invoice_save', ['only' => ['invoiceAction']]);
        /*** CRM - Dispute */
        $this->middleware('permission:CRM_Dispute_list|CRM_Dispute_revert-invoice', ['only' => ['crmDispute']]);
        $this->middleware('permission:CRM_Dispute_revert-invoice', ['only' => ['disputeAction']]);
        /*** CRM - Paid */
        $this->middleware('permission:CRM_Paid_list|CRM_Paid_open-close-cv', ['only' => ['crmPaid']]);
        $this->middleware('permission:CRM_Paid_open-close-cv', ['only' => ['paidAction']]);

//        $this->action_observer = new ActionObserver();
    }
    public function sentCv(){
        return view('administrator.crm.clear_cv');
    }

    public function crmSentCv()
    {
        $auth_user = Auth::user();

        $app_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
            ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('quality_notes.client_id', '=', 'histories.client_id');
                $join->on('quality_notes.sale_id', '=', 'histories.sale_id');
            })->select('quality_notes.details','quality_notes.quality_added_date','quality_notes.quality_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode','clients.app_phone', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website',DB::raw("(SELECT users.fullName from cv_notes INNER JOIN users on users.id = cv_notes.user_id
                WHERE cv_notes.client_id=clients.id AND cv_notes.sale_id=sales.id limit 1) as sent_by"))
            ->where([
                "clients.app_status" => "active",
                "quality_notes.status" => "active",
                "quality_notes.moved_tab_to" => "cleared",
                "histories.status" => "active"
            ])->whereIn("histories.sub_stage", ["quality_cleared", "crm_save"])
            ->orderBy("quality_notes.created_at","desc");


        $crm_cv_sent_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')

            ->whereIn("crm_notes.moved_tab_to", ["cv_sent", "cv_sent_saved"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        return datatables()->of($app_with_cvs)
            ->addColumn("agent_by", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->select('users.fullName')
                    ->first();
//                 dd($sent_by);
                return $sent_by ? $sent_by->fullName : "";
//                return $applicant->sent_by;
            })
            ->addColumn("app_job_title", function($applicant){
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
                return strtoupper($job_title_desc);
                // return strtoupper($applicant->app_job_title);
            })
            ->addColumn("app_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })





            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_cv_sent_save_note) {
                $content = '';
                if(!empty($crm_cv_sent_save_note)) {
                    foreach ($crm_cv_sent_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#clear_cv' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->app_phone . '"
                                           data-applicantNameJs="' . $applicant->app_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#clear_cv' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Reject/Request</a>';

                }
                $content .= '<a href="#" class="dropdown-item"
                data-toggle="modal"
                data-target="#revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '">
                <i class="icon-file-confirm"></i> Revert In Quality
            </a>';
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    /*** Revert In Quality ***/
                    $content .= '<div id="revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg" role="document">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h5 class="modal-title">Revert In Quality Notes</h5>';
                    $content .= '<button type="button" class="" style="background-color: black" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $content .= '</div>';
                    $quality_url = '/revert-cv-quality/';
                    $content .= '<form action="' . $quality_url . $applicant->id . '" method="POST" id="revert_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_quality_cv' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="col-form-label">Details</label>';
                    $content .= '<textarea name="details" id="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="Enter details.." required></textarea>';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-primary">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';

                    /*** Move CV Modal */
                    $content .= '<div id="clear_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">CRM Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="sent_cv_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="sent_cv_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .=  '<input type="hidden" name="cv_modal_name" class="model_name" value="sent_cv">';
                    $content .= '<textarea name="details" id="sent_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<select name="reject_reason" class="form-control crm_select_reason">';
                        $content .= '<option >Select Reason</option>';
                        $content .= '<option value="position_filled">Position Already Occupied</option>';
                        $content .= '<option value="agency">Referral from External Agency</option>';
                        $content .= '<option value="manager">Manager Disapproved</option>';
                        $content .= '<option value="no_response">Candidate Unreachable</option>';
                        $content .= '</select>';
                        $content .= '</div>';
                        $content .= '</div>';
                    }
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<button type="submit" name="cv_sent_reject" value="cv_sent_reject" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-danger bg-orange-800 legitRipple reject_btn sent_cv_submit" >Reject</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_request')) {
                        $content .= '<button type="submit" name="cv_sent_request" value="cv_sent_request" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple sent_cv_submit">Request</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_save')) {
                        $content .= '<button type="submit" name="cv_sent_save" value="cv_sent_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-teal legitRipple sent_cv_submit">Save</button>';
                    }
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Move CV Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                /*** Reject CV Modal
                $content .= '<div id="reject_cv'.$app_with_cv->id.'-'.$app_with_cv->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h5 class="modal-title">Reject CV Notes</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<form action="'.route('updateToRejectedCV',['id'=>$app_with_cv->id , 'viewString'=>'applicantWithSentCv']).'" method="GET" class="form-horizontal">';
                $content .= csrf_field();
                $content .= '<div class="modal-body">';
                $content .= '<div class="form-group row">';
                $content .= '<label class="col-form-label col-sm-3">Details</label>';
                $content .= '<div class="col-sm-9">';
                $content .= '<input type="hidden" name="job_hidden_id" value="'.$app_with_cv->sale_id.'">';
                $content .= '<input type="hidden" name="app_hidden_id" value="'.$app_with_cv->id.'">';
                $content .= '<textarea name="details" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple">Save</button>';
                $content .= '</div>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                 * /Reject CV Modal */

                return $content;
            })

            ->rawColumns(['agent_by','app_job_title','app_postcode','job_details','crm_note','action'])
            ->make(true);
    }

    public function getCrmNotesDetails($client_id,$sale_id){
        $auth_user = Auth::user()->id;
        $applicant = $client_id;
        $sale = $sale_id;

        //CV SENT Notes
        $cv_send_in_quality_notes = CvNote::where(array('client_id' => $applicant, 'sale_id' => $sale))->first();
        // ./CV SENT Notes

        // Quality Notes
        $applicant_in_quality = QualityNote::where(array('client_id' => $applicant,'sale_id' => $sale))->first();
        // ./Quality Notes

        //CRM Notes
        $applicant_in_crm = CrmNote::join('clients', 'crm_notes.client_id', '=', 'clients.id')
            ->select("clients.app_job_title","clients.app_name","clients.app_postcode","crm_notes.*")
            ->where(array('crm_notes.client_id' => $applicant, 'crm_notes.sale_id' => $sale))->orderBy('crm_notes.id', 'DESC')->get();
        // ./CRM Notes

        return view('administrator.crm.crm_note',
            compact('cv_send_in_quality_notes','applicant_in_quality','applicant_in_crm','client_id','sale_id'));
    }
    public function getCrmNotesDataTable($client_id, $sale_id) {
        $applicant_in_crm = CrmNote::join('clients', 'crm_notes.client_id', '=', 'clients.id')
            ->select("clients.app_job_title", "clients.app_name", "clients.app_postcode", "crm_notes.*")
            ->where(['crm_notes.client_id' => $client_id, 'crm_notes.sale_id' => $sale_id])
            ->orderBy('crm_notes.id', 'DESC')
            ->get();

        return response()->json($applicant_in_crm);
    }
    public function qualifiedStaff(){

        return view('administrator.crm.qualified_cv');

    }

    public function crmSentCvNurse()
    {
        $auth_user = Auth::user();

        $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
            ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('quality_notes.client_id', '=', 'histories.client_id');
                $join->on('quality_notes.sale_id', '=', 'histories.sale_id');
            })->select('quality_notes.details','quality_notes.quality_added_date','quality_notes.quality_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode','clients.app_phone', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "clients.app_job_category" => "nurses",
                "quality_notes.moved_tab_to" => "cleared",
                "quality_notes.status" => "active",
                "histories.status" => "active"
            ])->whereIn("histories.sub_stage", ["quality_cleared", "crm_save"])
            ->orderBy("quality_notes.created_at","desc");

        $crm_cv_sent_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            // ->join('history', function ($join) {
            //     $join->on('crm_notes.client_id', '=', 'history.client_id');
            //     $join->on('crm_notes.sale_id', '=', 'history.sale_id');
            // })
            ->where(["clients.app_job_category" => "nurses"])
            // ->whereIn("history.sub_stage", ["quality_cleared", "crm_save"])
            ->whereIn("crm_notes.moved_tab_to", ["cv_sent", "cv_sent_saved"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        return datatables()->of($applicant_with_cvs)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
//                dd($sent_by);
                return $sent_by ? $sent_by->fullName : "";
            })

            ->addColumn("applicant_job_title", function($applicant){
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
                return strtoupper($job_title_desc);
                // return strtoupper($applicant->applicant_job_title);
            })

            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })



            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_cv_sent_save_note) {
                $content = '';
                if(!empty($crm_cv_sent_save_note)) {
                    foreach ($crm_cv_sent_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#clear_cv_nurse' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->app_phone . '"
                                           data-applicantNameJs="' . $applicant->app_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#clear_cv_nurse' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Reject/Request</a>';

                    $content .= '<a href="#" class="dropdown-item"
                data-toggle="modal"
                data-target="#revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '">
                <i class="icon-file-confirm"></i> Revert In Quality
            </a>';
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details_sent_nurse'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details_sent_nurse'.$applicant->id.'-'.$applicant->sale_id.'">';
                    $content .= '<i class="icon-file-confirm"></i>Manager Details</a>';


                }
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    /*** Revert In Quality ***/
                    $content .= '<div id="revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg" role="document">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h5 class="modal-title">Revert In Quality Notes</h5>';
                    $content .= '<button type="button" class="" style="background-color: black" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $content .= '</div>';
                    $quality_url = '/revert-cv-quality/';
                    $content .= '<form action="' . $quality_url . $applicant->id . '" method="POST" id="revert_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_quality_cv' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="col-form-label">Details</label>';
                    $content .= '<textarea name="details" id="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="Enter details.." required></textarea>';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-primary">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';

                    /*** Move CV Modal */
                    $content .= '<div id="clear_cv_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">CRM Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="sent_cv_form_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="sent_cv_alert_nurse' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .=  '<input type="hidden" name="cv_modal_name" class="model_name" value="sent_cv_nurse">';
                    $content .= '<textarea name="details" id="sent_cv_details_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<select name="reject_reason" class="form-control crm_select_reason">';
                        $content .= '<option >Select Reason</option>';
                        $content .= '<option value="position_filled">Position Already Occupied</option>';
                        $content .= '<option value="agency">Referral from External Agency</option>';
                        $content .= '<option value="manager">Manager Disapproved</option>';
                        $content .= '<option value="no_response">Candidate Unreachable</option>';
                        $content .= '</select>';
                        $content .= '</div>';
                        $content .= '</div>';
                    }
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<button type="submit" name="cv_sent_reject" value="cv_sent_reject" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-danger bg-orange-800 legitRipple reject_btn sent_cv_submit" >Reject</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_request')) {
                        $content .= '<button type="submit" name="cv_sent_request" value="cv_sent_request" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple sent_cv_submit">Request</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_save')) {
                        $content .= '<button type="submit" name="cv_sent_save" value="cv_sent_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-teal legitRipple sent_cv_submit">Save</button>';
                    }
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Move CV Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details_sent_nurse'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade manager_details" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                /*** Reject CV Modal
                $content .= '<div id="reject_cv'.$applicant_with_cv->id.'-'.$applicant_with_cv->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h5 class="modal-title">Reject CV Notes</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<form action="'.route('updateToRejectedCV',['id'=>$applicant_with_cv->id , 'viewString'=>'applicantWithSentCv']).'" method="GET" class="form-horizontal">';
                $content .= csrf_field();
                $content .= '<div class="modal-body">';
                $content .= '<div class="form-group row">';
                $content .= '<label class="col-form-label col-sm-3">Details</label>';
                $content .= '<div class="col-sm-9">';
                $content .= '<input type="hidden" name="job_hidden_id" value="'.$applicant_with_cv->sale_id.'">';
                $content .= '<input type="hidden" name="app_hidden_id" value="'.$applicant_with_cv->id.'">';
                $content .= '<textarea name="details" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple">Save</button>';
                $content .= '</div>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                 * /Reject CV Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action'])
            ->make(true);
    }

    public function nonQualifiedStaff(){

        return view('administrator.crm.non_qualified_cv');

    }

    public function crmSentCvNonNurse()
    {
        $auth_user = Auth::user();
        $applicant_with_cvs = Client::join('quality_notes', 'clients.id', '=', 'quality_notes.client_id')
            ->join('sales', 'quality_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('quality_notes.client_id', '=', 'histories.client_id');
                $join->on('quality_notes.sale_id', '=', 'histories.sale_id');
            })->select('quality_notes.details','quality_notes.quality_added_date','quality_notes.quality_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode','clients.app_phone', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "clients.app_job_category" => "non-nurses",
                "quality_notes.moved_tab_to" => "cleared",
                "quality_notes.status" => "active",
                "histories.status" => "active"
            ])->whereIn("histories.sub_stage", ["quality_cleared", "crm_save"])
            ->orderBy("quality_notes.created_at","desc");

        $crm_cv_sent_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            // ->join('history', function ($join) {
            //     $join->on('crm_notes.client_id', '=', 'history.client_id');
            //     $join->on('crm_notes.sale_id', '=', 'history.sale_id');
            // })
            ->where("clients.app_job_category","non-nurses")
            // ->whereIn("history.sub_stage", ["quality_cleared", "crm_save"])
            ->whereIn("crm_notes.moved_tab_to", ["cv_sent", "cv_sent_saved"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();
//        dd($crm_cv_sent_save_note);

        return datatables()->of($applicant_with_cvs)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function($applicant){
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
                return strtoupper($job_title_desc);
                // return strtoupper($applicant->applicant_job_title);
            })
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })


            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_cv_sent_save_note) {
                $content = '';
                if(!empty($crm_cv_sent_save_note)) {
                    foreach ($crm_cv_sent_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })

            ->addColumn("action", function ($applicant) use ($auth_user) {
                $applicant_msgs ='';

                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option sms_action_sent_cv"
                                           data-controls-modal="#clear_cv_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->app_phone . '"
                                           data-applicantNameJs="' . $applicant->app_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-applicantunitjs="' . $applicant->unit_name . '"
                                           data-target="#clear_cv_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Reject/Request</a>';

                    $content .= '<a href="#" class="dropdown-item"
                data-toggle="modal"
                data-target="#revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '">
                <i class="icon-file-confirm"></i> Revert In Quality
            </a>';
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details_sent_non_nurse'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details_sent_non_nurse'.$applicant->id.'-'.$applicant->sale_id.'">';
                    $content .= '<i class="icon-file-confirm"></i>Manager Details</a>';
                }

                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Sent-CVs_request','CRM_Sent-CVs_save','CRM_Sent-CVs_reject'])) {
                    /*** Revert In Quality ***/

                    $content .= '<div id="revert_in_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg" role="document">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h5 class="modal-title">Revert In Quality Notes</h5>';
                    $content .= '<button type="button" class="" style="background-color: black" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $content .= '</div>';
                    $quality_url = '/revert-cv-quality/';
                    $content .= '<form action="' . $quality_url . $applicant->id . '" method="POST" id="revert_quality' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_quality_cv' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="col-form-label">Details</label>';
                    $content .= '<textarea name="details" id="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="Enter details.." required></textarea>';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $content .= '<button type="submit" class="btn btn-primary">Save</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';


                    /*** Move CV Modal */
                    $content .= '<div id="clear_cv_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">CRM Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="sent_cv_form_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="sent_cv_alert_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" id="non_nurse_app_hidden_id" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" id="non_nurse_job_hidden_id" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .=  '<input type="hidden" name="client_id_chat" id="client_id_chat">';
                    $content .=  '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .=  '<input type="hidden" name="cv_modal_name" class="model_name" value="sent_cv_non_nurse">';
                    $content .= '<textarea name="details" id="sent_cv_details_non_nurse' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<div class="form-group row">';
                        $content .= '<label class="col-form-label col-sm-3">Choose type:</label>';
                        $content .= '<div class="col-sm-9">';
                        $content .= '<select name="reject_reason" class="form-control crm_select_reason">';
                        $content .= '<option >Select Reason</option>';
                        $content .= '<option value="position_filled">Position Already Occupied</option>';
                        $content .= '<option value="agency">Referral from External Agency</option>';
                        $content .= '<option value="manager">Manager Disapproved</option>';
                        $content .= '<option value="no_response">Candidate Unreachable</option>';
                        $content .= '</select>';
                        $content .= '</div>';
                        $content .= '</div>';
                    }
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_reject')) {
                        $content .= '<button type="submit" name="cv_sent_reject" value="cv_sent_reject" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" data-model_name="sent_cv" class="btn btn-danger bg-orange-800 legitRipple reject_btn sent_cv_submit" >Reject</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_request')) {
                        $content .= '<button type="submit" name="cv_sent_request" value="cv_sent_request" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark cv_sent_request legitRipple sent_cv_submit">Request</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Sent-CVs_save')) {
                        $content .= '<button type="submit" name="cv_sent_save" value="cv_sent_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-teal legitRipple sent_cv_submit">Save</button>';
                    }
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Move CV Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details_sent_non_nurse'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                /*** Reject CV Modal
                $content .= '<div id="reject_cv'.$applicant_with_cv->id.'-'.$applicant_with_cv->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header">';
                $content .= '<h5 class="modal-title">Reject CV Notes</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<form action="'.route('updateToRejectedCV',['id'=>$applicant_with_cv->id , 'viewString'=>'applicantWithSentCv']).'" method="GET" class="form-horizontal">';
                $content .= csrf_field();
                $content .= '<div class="modal-body">';
                $content .= '<div class="form-group row">';
                $content .= '<label class="col-form-label col-sm-3">Details</label>';
                $content .= '<div class="col-sm-9">';
                $content .= '<input type="hidden" name="job_hidden_id" value="'.$applicant_with_cv->sale_id.'">';
                $content .= '<input type="hidden" name="app_hidden_id" value="'.$applicant_with_cv->id.'">';
                $content .= '<textarea name="details" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-link legitRipple" data-dismiss="modal">Close</button>';
                $content .= '<button type="submit" class="btn bg-teal legitRipple">Save</button>';
                $content .= '</div>';
                $content .= '</form>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                 * /Reject CV Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public  function rejectCv(){
        return view('administrator.crm.reject_cv');
    }
    public function crmRejectCv()
    {
        $auth_user = Auth::user();
        $applicant_with_rejected_cvs = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date', 'sales.send_cv_limit',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "crm_notes.moved_tab_to" => "cv_sent_reject", "crm_notes.status" => "active",
                "histories.status" => "active", "histories.sub_stage" => "crm_reject"
            ])
//->whereIn('crm_notes.id', function($query){
//                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="cv_sent_reject" and sale_id=sales.id and histories.id=client_id'));
//            })
            ->orderBy("crm_notes.created_at","DESC");


        return datatables()->of($applicant_with_rejected_cvs)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function($applicant){
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
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) {
                $content = '<p><b>DATE: </b>'.$applicant->crm_added_date.'<b> TIME: </b>'.$applicant->crm_added_time.'</p><p><b>NOTE: </b>'.$applicant->details.'</p>';

                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $applicant_msgs ='';

                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';

                if ($auth_user->hasPermissionTo('CRM_Rejected-CV_revert-sent-cv')) {
                    $content .= '<a href="#" class="dropdown-item"
                                       data-controls-modal="#revert_sent_cvs' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                       data-keyboard="false" data-toggle="modal"
                                       data-target="#revert_sent_cvs' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Revert </a>';
                }
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';

                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';

                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                if ($auth_user->hasPermissionTo('CRM_Rejected-CV_revert-sent-cv')) {
                    $sent_cv_count = CvNote::where(['sale_id' => $applicant->sale_id, 'status' => 'active'])->count();
                    /*** Revert To Sent CVs Modal */
                    $content .= '<div id="revert_sent_cvs' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Rejected CV Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="revert_sent_cv_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_sent_cv_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Share CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$applicant->send_cv_limit.'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" id="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="revert_sent_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="submit" name="rejected_cv_revert_sent_cvs" value="rejected_cv_revert_sent_cvs" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple rejected_cv_submit">Share CV</button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Revert To Sent CVs Modal */
                }

                return $content;
            })
            ->addColumn('office_name', function ($applicant) {
                $sale = Sale::where('id', $applicant->sale_id)->first();
                $officeName = $sale->office->name; // Assuming 'name' is the attribute in your Office model that holds the office name
                return $officeName;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }

    public function requestNurse(){
        return view('administrator.crm.request_cv');

    }
    public function crmRequestNurse()
    {
        $auth_user = Auth::user();
        $crm_request_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })
            ->whereIn("clients.app_job_category",["nurses","chef"])
//            ->where("applicants.job_category","nurse")
            ->whereIn("histories.sub_stage", ["crm_request", "crm_request_save","revert_to_crm_request"])
            ->whereIn("crm_notes.moved_tab_to", ["cv_sent_request", "request_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();
        $applicant_cvs_in_request = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->leftJoin('interviews', function ($join) {
                $join->on('clients.id', '=', 'interviews.client_id');
                $join->on('sales.id', '=', 'interviews.sale_id');
                $join->where('interviews.status', '=', 'active');
            })->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website',
                'interviews.schedule_time', 'interviews.schedule_date', 'interviews.status as interview_status')
            ->where([
                "clients.app_status" => "active",
//                "applicants.job_category" => "nurse",
                "crm_notes.moved_tab_to" => "cv_sent_request", "crm_notes.status" => "active",
                "histories.status" => "active"
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="cv_sent_request" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn("histories.sub_stage", ["crm_request", "crm_request_save","revert_to_crm_request"])
            ->whereIn("clients.app_job_category",["nurses","chef"])

            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($applicant_cvs_in_request)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })

            ->addColumn("applicant_job_title", function($applicant){
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
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_request_save_note) {
                $content = '';
                if(!empty($crm_request_save_note)) {
                    foreach ($crm_request_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasPermissionTo('CRM_Request_schedule-interview')) {
                    if ($applicant->schedule_time && $applicant->schedule_date && $applicant->interview_status == 'active') {
                        $content .= '<a href="#" class="disabled dropdown-item"><i class="icon-file-confirm"></i>Schedule Interview</a>';
                    } else {
                        $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '"
                                           data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                           data-target="#schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '">';
                        $content .= '<i class="icon-file-confirm"></i>Schedule Interview</a>';
                    }
                }
                if ($auth_user->hasAnyPermission(['CRM_Request_reject','CRM_Request_confirm','CRM_Request_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Move To Confirmation</a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';

                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasPermissionTo('CRM_Request_schedule-interview')) {
                    /*** Schedule Interview Modal */
                    $content .= '<div id="schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h3 class="modal-title">' . $applicant->app_name . '</h3>';
                    $content .= '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="schedule_interview_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<form action="' . route('scheduleInterview') . '" method="POST" id="schedule_interview_form' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation" novalidate>';
                    $content .= csrf_field();
                    $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="schedule_date' . $applicant->id . '-' . $applicant->sale_id . '">Select Schedule Date</label>';
                    $content .= '<input type="date" class="form-control" name="schedule_date" id="schedule_date' . $applicant->id . '-' . $applicant->sale_id . '" placeholder="Select Schedule Date" required>';
                    $content .= '<div class="invalid-feedback">Please provide a valid date.</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="schedule_time' . $applicant->id . '-' . $applicant->sale_id . '">Select Schedule Time</label>';
                    $content .= '<input type="time" class="form-control" id="schedule_time' . $applicant->id . '-' . $applicant->sale_id . '" name="schedule_time" placeholder="Type Schedule Time e.g., 10:00" required>';
                    $content .= '<div class="invalid-feedback">Please provide a valid time.</div>';
                    $content .= '</div>';
                    $content .= '<button type="submit" class="btn btn-primary btn-block">Schedule</button>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';

                    /*** /Schedule Interview Modal */
                }

                if ($auth_user->hasAnyPermission(['CRM_Request_reject','CRM_Request_confirm','CRM_Request_save'])) {
                    /*** Confirmation CV Modal */
                    $content .= '<div id="confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Confirm CV Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="request_cv_form' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation" novalidate>';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="request_cv_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="request_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '<div class="invalid-feedback">Please provide details.</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Request_reject')) {
                        $content .= '<button type="submit" name="request_reject" value="request_reject" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-danger legitRipple request_cv_submit">Reject</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Request_confirm')) {
                        $disabled = "disabled";
                        if ($applicant->schedule_time && $applicant->schedule_date && $applicant->interview_status == 'active') {
                            $disabled = "";
                        }
                        $content .= '<button type="submit" name="request_to_confirm" value="request_to_confirm" ' . $disabled . ' data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success legitRipple request_cv_submit">Confirm</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Request_save')) {
                        $content .= '<button type="submit" name="request_to_save" value="request_to_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary legitRipple request_cv_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';

                    /*** /Confirmation CV Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';

                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function requestNonNurse(){
        return view('administrator.crm.request_non_cv');

    }
    public function crmRequestNonNurse()
    {
        $auth_user = Auth::user();
        $crm_request_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })
            ->where("clients.app_job_category","non-nurses")
            ->whereIn("histories.sub_stage", ["crm_request", "crm_request_save"])
            ->whereIn("crm_notes.moved_tab_to", ["cv_sent_request", "request_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();
        $applicant_cvs_in_request = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->leftJoin('interviews', function ($join) {
                $join->on('clients.id', '=', 'interviews.client_id');
                $join->on('sales.id', '=', 'interviews.sale_id');
                $join->where('interviews.status', '=', 'active');
            })->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website',
                'interviews.schedule_time', 'interviews.schedule_date', 'interviews.status as interview_status')
            ->where([
                "clients.app_status" => "active",
                "clients.app_job_category" => "non-nurses",
                "crm_notes.moved_tab_to" => "cv_sent_request", "crm_notes.status" => "active",
                "histories.status" => "active"
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="cv_sent_request" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn("histories.sub_stage", ["crm_request", "crm_request_save"])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($applicant_cvs_in_request)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function($applicant){
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
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })

            ->addColumn("crm_note", function ($applicant) use ($crm_request_save_note) {
                $content = '';
                if(!empty($crm_request_save_note)) {
                    foreach ($crm_request_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasPermissionTo('CRM_Request_schedule-interview')) {
                    if ($applicant->schedule_time && $applicant->schedule_date && $applicant->interview_status == 'active') {
                        $content .= '<a href="#" class="disabled dropdown-item"><i class="icon-file-confirm"></i>Schedule Interview</a>';
                    } else {
                        $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '"
                                           data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                           data-target="#schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '">';
                        $content .= '<i class="icon-file-confirm"></i>Schedule Interview</a>';
                    }
                }
                if ($auth_user->hasAnyPermission(['CRM_Request_reject','CRM_Request_confirm','CRM_Request_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Move To Confirmation</a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasPermissionTo('CRM_Request_schedule-interview')) {
                    /*** Schedule Interview Modal */
                    $content .= '<div id="schedule_interview' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header bg-primary text-white">';
                    $content .= '<h3 class="modal-title">' . $applicant->app_name . '</h3>';
                    $content .= '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="schedule_interview_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<form action="' . route('scheduleInterview') . '" method="POST" id="schedule_interview_form' . $applicant->id . '-' . $applicant->sale_id . '" class="needs-validation" novalidate>';
                    $content .= csrf_field();
                    $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="schedule_date' . $applicant->id . '-' . $applicant->sale_id . '">Select Schedule Date</label>';
                    $content .= '<input type="date" class="form-control" name="schedule_date" id="schedule_date' . $applicant->id . '-' . $applicant->sale_id . '" placeholder="Select Schedule Date" required>';
                    $content .= '<div class="invalid-feedback">Please provide a valid date.</div>';
                    $content .= '</div>';
                    $content .= '<div class="form-group">';
                    $content .= '<label for="schedule_time' . $applicant->id . '-' . $applicant->sale_id . '">Select Schedule Time</label>';
                    $content .= '<input type="time" class="form-control" id="schedule_time' . $applicant->id . '-' . $applicant->sale_id . '" name="schedule_time" placeholder="Type Schedule Time e.g., 10:00" required>';
                    $content .= '<div class="invalid-feedback">Please provide a valid time.</div>';
                    $content .= '</div>';
                    $content .= '<button type="submit" class="btn btn-primary btn-block">Schedule</button>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Schedule Interview Modal */
                }

                if ($auth_user->hasAnyPermission(['CRM_Request_reject','CRM_Request_confirm','CRM_Request_save'])) {
                    /*** Confirmation CV Modal */
                    $content .= '<div id="confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Confirm CV Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="request_cv_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="request_cv_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<input type="hidden" name="applicant_id_chat" id="applicant_id_chat">';
                    $content .= '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .= '<input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">';
                    $content .= '<textarea name="details" id="request_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Request_reject')) {
                        $content .= '<button type="submit" name="request_reject" value="request_reject" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-danger legitRipple request_cv_submit">Reject</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Request_confirm')) {
                        $disabled = ($applicant->schedule_time && $applicant->schedule_date && $applicant->interview_status == 'active') ? '' : 'disabled';
                        $content .= '<button type="submit" name="request_to_confirm" value="request_to_confirm" ' . $disabled . ' data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success legitRipple request_cv_submit">Confirm</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Request_save')) {
                        $content .= '<button type="submit" name="request_to_save" value="request_to_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary legitRipple request_cv_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Confirmation CV Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function rejectRequestCv(){
        return view('administrator.crm.reject_request_cv');

    }

    public function crmRejectByRequest()
    {
        $auth_user = Auth::user();


        $reject_by_request = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.send_cv_limit',
                'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name', 'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline',
                'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "crm_notes.moved_tab_to" => "request_reject",
                "histories.sub_stage" => "crm_request_reject", "histories.status" => "active"
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="request_reject" and sale_id=sales.id and clients.id=client_id'));
            })->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($reject_by_request)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })

            ->addColumn("applicant_job_title", function($applicant){
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
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) {
                $content = '';
                $content .= '<p><b>DATE: </b>';
                $content .= $applicant->crm_added_date;
                $content .= '<b> TIME: </b>';
                $content .= $applicant->crm_added_time;
                $content .= '</p>';
                $content .= '<p><b>NOTE: </b>';
                $content .= $applicant->details;
                $content .= '</p>';

                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';

                if ($auth_user->hasAnyPermission(['CRM_Rejected-By-Request_revert-sent-cv','CRM_Rejected-By-Request_revert-request'])) {
//                   if($auth_user->hasAnyPermission(['CRM_Revert_quality-btn'])){
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#revert' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#revert' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Revert </a>';
//                }
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered"">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                if ($auth_user->hasAnyPermission(['CRM_Rejected-By-Request_revert-sent-cv','CRM_Rejected-By-Request_revert-request'])) {
                    $sent_cv_count = CvNote::where(['sale_id' => $applicant->sale_id, 'status' => 'active'])->count();
                    /*** Revert To Sent CVs Modal */
                    $content .= '<div id="revert' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Rejected CV Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="revert_cv_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_cv_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Sent CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$applicant->send_cv_limit.'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="revert_cv_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Rejected-By-Request_revert-sent-cv')) {
                        $content .= '<button type="submit" name="rejected_request_revert_sent_cvs" value="rejected_request_revert_sent_cvs" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple revert_cv_submit"> Sent CV </button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Rejected-By-Request_revert-request')) {
                        $content .= '<button type="submit" name="rejected_request_revert_request" value="rejected_request_revert_request" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-teal  greenButton revert_cv_submit"> Request </button>';
                    }
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Revert To Sent CVs Modal */
                }

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }

    public function crmConfirmCv(){
        return view('administrator.crm.confrim_cv');

    }

    public function crmConfirmation()
    {
        $auth_user = Auth::user();
        $crm_confirm_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_request_confirm", "crm_interview_save"])
            ->whereIn("crm_notes.moved_tab_to", ["request_confirm", "interview_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $is_in_crm_confirm = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('interviews', function ($join) {
                $join->on('clients.id', '=', 'interviews.client_id');
                $join->on('crm_notes.sale_id', '=', 'interviews.sale_id');
            })->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website',
                'interviews.schedule_time', 'interviews.schedule_date')
            ->where([
                "clients.app_status" => "active",
                "crm_notes.moved_tab_to" => "request_confirm",
                "interviews.status" => "active",
                "histories.status" => "active"
            ])->whereIn('histories.sub_stage', ['crm_request_confirm', 'crm_interview_save'])
            ->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="request_confirm" and sale_id=sales.id and clients.id=client_id'));
            })->orderBy("crm_notes.created_at","DESC");
        //dd($is_in_crm_confirm->count());

        return datatables()->of($is_in_crm_confirm)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();

                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("interview_schedule", function ($applicant) {
                return $applicant->schedule_date.'<br><a href="#" style="margin-left: 15px;">'.$applicant->schedule_time.'</a>';
            })
            ->addColumn("applicant_job_title", function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    // $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->job_title_prof)->first();
                    // $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->app_job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;
            })
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })


            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_confirm_save_note) {
                $content = '';
                if(!empty($crm_confirm_save_note)) {
                    foreach ($crm_confirm_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Confirmation_revert-request','CRM_Confirmation_not-attended','CRM_Confirmation_attend','CRM_Confirmation_rebook','CRM_Confirmation_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#confirm_cv' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->app_phone . '"
                                           data-applicantNameJs="' . $applicant->app_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#after_interview' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Confirmation_revert-request','CRM_Confirmation_not-attended','CRM_Confirmation_attend','CRM_Confirmation_rebook','CRM_Confirmation_save'])) {
                    /*** After Interview Note Modal */
                    $content .= '<div id="after_interview' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Interview Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="after_interview_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="after_interview_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<input type="hidden" name="applicant_id_chat" id="applicant_id_chat">';
                    $content .= '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .= '<input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">';
                    $content .= '<textarea name="details" id="after_interview_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Confirmation_revert-request')) {
                        $content .= '<button type="submit" name="confirm_revert_request" value="confirm_revert_request" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple after_interview_submit">Revert Request</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Confirmation_not-attended')) {
                        $content .= '<button type="submit" name="interview_not_attend" value="interview_not_attend" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-warning btn-rounded legitRipple after_interview_submit">Not Attend</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Confirmation_attend')) {
                        $content .= '<button type="submit" name="interview_attend" value="interview_attend" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary btn-rounded legitRipple after_interview_submit">Attend</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Confirmation_rebook')) {
                        $content .= '<button type="submit" name="interview_rebook" value="interview_rebook" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-info btn-rounded legitRipple after_interview_submit">Rebook</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Confirmation_save')) {
                        $content .= '<button type="submit" name="interview_save" value="interview_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple after_interview_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /After Interview Note Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered"">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })

            ->addColumn('office_name', function ($applicant) {
                $sale = Sale::where('id', $applicant->sale_id)->first();
                $officeName = $sale->office->name; // Assuming 'name' is the attribute in your Office model that holds the office name
                return $officeName;
            })
            ->editColumn('schedule_search', function($applicant)
            {
                $date_new=strtotime($applicant->schedule_date);
                return date('d-m-Y', $date_new);
            })->rawColumns(['name','interview_schedule','applicant_job_title','applicant_postcode','job_details','crm_note','action','schedule_search','office_name'])
            ->make(true);
    }

    public function crmRebookCv(){
        return view('administrator.crm.rebook_cv');
    }
    public function crmRebook()
    {
        $auth_user = Auth::user();
        $crm_rebook_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_rebook", "crm_rebook_save"])
            ->whereIn("crm_notes.moved_tab_to", ["rebook", "rebook_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $is_in_crm_rebook = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "crm_notes.moved_tab_to" => "rebook",
                "histories.status" => "active"
            ])->whereIn('histories.sub_stage', ['crm_rebook', 'crm_rebook_save'])
            ->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="rebook" and sale_id=sales.id and clients.id=client_id'));
            })
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($is_in_crm_rebook)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_rebook_save_note) {
                $content = '';
                if(!empty($crm_rebook_save_note)) {
                    foreach ($crm_rebook_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Rebook_not-attended','CRM_Rebook_attend','CRM_Rebook_save'])) {
                    $content .= '<a href="#" class="dropdown-item testing_href sms_action_option"
                                           data-controls-modal="#rebook' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-name="' . $applicant->app_name . '"
                                           data-target="#rebook' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Rebook_not-attended','CRM_Rebook_attend','CRM_Rebook_save'])) {
                    /*** Rebook Note Modal */
                    $content .= '<div id="rebook' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade reebok_confirm" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Rebook Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="rebook_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="rebook_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="test_val" id="test_val">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .=  '<input type="hidden" name="applicant_id_chat" id="applicant_id_chat">';
                    $content .=  '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .=  '<input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">';
                    $content .=  '<input type="hidden" name="cv_modal_name" class="model_name" value="rebook">';
                    $content .= '<textarea name="details" id="rebook_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Request_confirm')) {
                        $content .= '<button type="submit" name="rebook_confirm" value="rebook_confirm" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary btn-rounded legitRipple rebook_submit" >Confirmation</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Rebook_not-attended')) {
                        $content .= '<button type="submit" name="rebook_not_attend" value="rebook_not_attend" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-warning btn-rounded legitRipple rebook_submit">Not Attend</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Rebook_attend')) {
                        $content .= '<button type="submit" name="rebook_attend" value="rebook_attend" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark btn-rounded legitRipple rebook_submit">Attend</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Rebook_save')) {
                        $content .= '<button type="submit" name="rebook_save" value="rebook_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple rebook_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Rebook Note Modal */
                    $content .= '<div id="schedule_interviewww" class="modal fade" >';
                    $content .= '<div class="modal-dialog modal-sm">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h3 class="modal-title" id="schdule_applicant_name"></h3>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="schedule_interview_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<form action="' . route('scheduleInterview') . '" method="POST" id="schedule_interview_form_reebok' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<input type="hidden" name="detail_value" id="detail_value" >';
                    $content .= '<input type="hidden" name="rebook_applicant_id" id="rebook_applicant_id" >';
                    $content .= '<input type="hidden" name="rebook_sale_id" id="rebook_sale_id" >';
                    $content .= '<input type="hidden" name="applicant_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="sale_id" value="' . $applicant->sale_id . '">';
                    $content .= '<div class="mb-4">';
                    $content .= '<div class="input-group">';
                    $content .= '<span class="input-group-prepend">';
                    $content .= '<span class="input-group-text"><i class="icon-calendar5"></i></span>';
                    $content .= '</span>';
                    $content .= '<input type="text" class="form-control pickadate-year" name="schedule_date_reebok" id="schedule_date_reebok' . $applicant->id . '-' . $applicant->sale_id . '" placeholder="Select Schedule Date">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="mb-4">';
                    $content .= '<div class="input-group">';
                    $content .= '<span class="input-group-prepend">';
                    $content .= '<span class="input-group-text"><i class="icon-watch2"></i></span>';
                    $content .= '</span>';
//                $content .= '<input type="text" class="form-control time_pickerrrr" id="anytime-time'.$applicant->id.'-'.$applicant->sale_id.'" name="schedule_time" placeholder="Select Schedule Time e.g., 00:00">';
                    $content .= '<input type="text" class="form-control" id="schedule_time_reebok' . $applicant->id . '-' . $applicant->sale_id . '" name="schedule_time_reebok" placeholder="Type Schedule Time e.g., 10:00">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<button type="button" class="btn bg-teal" id="schedule_rebook" >Schedule Confirmation</button>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })


            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmPreStartCv(){
        return view('administrator.crm.pre_start_cv');
    }
    public function crmPreStartDate()
    {
        $auth_user = Auth::user();
        $crm_attend_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_interview_attended", "crm_prestart_save"])
            ->whereIn("crm_notes.moved_tab_to", ["interview_attended", "prestart_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $attended = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'interview_attended',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="interview_attended" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn('histories.sub_stage', ['crm_interview_attended', 'crm_prestart_save'])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($attended)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })


            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_attend_save_note) {
                $content = '';
                if(!empty($crm_attend_save_note)) {
                    foreach ($crm_attend_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<div class="note">';
                            $content .= '<p><b>Date:</b> ' . $crm_save->crm_added_date . '</p>';
                            $content .= '<p><b>Time:</b> ' . $crm_save->crm_added_time . '</p>';
                            $content .= '<p><b>Note:</b> ' . $crm_save->crm_note_details . '</p>';
                            $content .= '</div>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Attended_start-date','CRM_Attended_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#accept' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#accept' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Attended_start-date','CRM_Attended_save','CRM_Attended_decline'])) {
                    /*** Accept Modal */
                    $content .= '<div id="accept' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Start Date Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="accept_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="accept_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<input type="hidden" name="applicant_id_chat" id="applicant_id_chat">';
                    $content .= '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .= '<input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">';
                    $content .= '<textarea name="details" id="accept_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Confirmation_rebook')) {
                        $content .= '<button type="submit" name="Confirmation_rebook" value="Confirmation_rebook" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary accept_submit">Rebook</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Attended_decline')) {
                        $content .= '<button type="submit" name="decline" value="decline" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-warning accept_submit">Decline</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Attended_start-date')) {
                        $content .= '<button type="submit" name="start_date" value="start_date" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark accept_submit">Start Date</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Attended_save')) {
                        $content .= '<button type="submit" name="prestart_save" value="prestart_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Accept Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group">';
                $content .= '<li class="list-group-item "><p><b>Name: </b><span class="' . ($applicant->unit_name ? 'text-primary' : 'text-muted') . '">'.$applicant->unit_name.'</span></p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b><span class="' . ($applicant->contact_email ? 'text-primary' : 'text-muted') . '">'.$applicant->contact_email.'</span></p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** /Manager Details Modal */

                return $content;
            })

            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }

    public function crmDeclinedCv(){
        return view('administrator.crm.declined_cv');
    }
    public function crmDeclined()
    {
        $auth_user = Auth::user();
        $crm_attend_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->where("histories.sub_stage", "=", "crm_declined")
            ->where("crm_notes.moved_tab_to", "=", "declined")
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $attended = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'declined',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="declined" and sale_id=sales.id and clients.id=client_id'));
            })->where('histories.sub_stage', '=', 'crm_declined')
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($attended)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
                $job_title_desc='';
                if($applicant->app_job_title_prof!=null)
                {
                    $job_prof_res = Specialist_job_titles::select('id','name')->where('id', $applicant->job_title_prof)->first();
                    $job_title_desc = $applicant->app_job_title.' ('.$job_prof_res->name.')';
                }
                else
                {

                    $job_title_desc = $applicant->app_job_title;
                }
                return $job_title_desc;
            })
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })



            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_attend_save_note) {
                $content = '';
                if(!empty($crm_attend_save_note)) {
                    foreach ($crm_attend_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<div class="note">';
                            $content .= '<p><b>Date:</b> ' . $crm_save->crm_added_date . '</p>';
                            $content .= '<p><b>Time:</b> ' . $crm_save->crm_added_time . '</p>';
                            $content .= '<p><b>Note:</b> ' . $crm_save->crm_note_details . '</p>';
                            $content .= '</div>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasPermissionTo('CRM_Declined_revert-to-attended')) {
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#declined_revert' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#declined_revert' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Revert </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';

                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasPermissionTo('CRM_Declined_revert-to-attended')) {
                    /*** Revert Modal */
                    $content .= '<div id="declined_revert' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Decline Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="declined_revert_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="declined_revert_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="declined_revert_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Declined_revert-to-attended')) {
                        $content .= '<button type="submit" name="declined_revert_attended" value="declined_revert_attended" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple declined_submit"> Attended </button>';
                    }
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Revert Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })

            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmNotAttendedCv(){
        return view('administrator.crm.not_attend_cv');
    }
    public function crmNotAttended()
    {
        $auth_user = Auth::user();

        $not_attended = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date', 'sales.send_cv_limit',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                "clients.app_status" => "active",
                "crm_notes.moved_tab_to" => "interview_not_attended",
                "histories.sub_stage" => "crm_interview_not_attended", "histories.status" => "active"
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="interview_not_attended" and sale_id=sales.id and clients.id=client_id'));
            })->orderBy("crm_notes.created_at","DESC");
//        dd($not_attended);

        return datatables()->of($not_attended)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function($applicant){
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
            ->addColumn("applicant_postcode", function($applicant){
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })


            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) {
                $content = '';
                $content .= '<p><b>DATE: </b>';
                $content .= $applicant->crm_added_date;
                $content .= '<b> TIME: </b>';
                $content .= $applicant->crm_added_time;
                $content .= '</p>';
                $content .= '<p><b>NOTE: </b>';
                $content .= $applicant->details;
                $content .= '</p>';

                return $content;
            })

            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager Details</a>';
                if ($auth_user->hasPermissionTo('CRM_Not-Attended_revert-to-attended')) {
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#revert_attended' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#revert_attended' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>Revert </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item active"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                if ($auth_user->hasPermissionTo('CRM_Not-Attended_revert-to-attended')) {
                    $sent_cv_count = CvNote::where(['sale_id' => $applicant->sale_id, 'status' => 'active'])->count();
                    /*** Revert To Attend Modal */
                    $content .= '<div id="revert_attended' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Not Attended Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="revert_attended_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_attended_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Sent CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$applicant->send_cv_limit.'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="revert_attended_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="submit" name="back_to_attended" value="back_to_attended" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple revert_attended_submit"> Attended </button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Revert To Attend Modal */
                }

                return $content;
            })

            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmStartDataCV(){
        return view('administrator.crm.start_date_cv');
    }
    public function crmStartDate()
    {
        $auth_user = Auth::user();
        $crm_start_date_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_start_date", "crm_start_date_save", "crm_start_date_back"])
            ->whereIn("crm_notes.moved_tab_to", ["start_date", "start_date_save", "start_date_back"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $start_date = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'start_date',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="start_date" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn('histories.sub_stage', ['crm_start_date', 'crm_start_date_save', 'crm_start_date_back'])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($start_date)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })


            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_start_date_save_note) {
                $content = '';
                if(!empty($crm_start_date_save_note)) {
                    foreach ($crm_start_date_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Start-Date_invoice','CRM_Start-Date_start-date-hold','CRM_Start-Date_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#start_date' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->app_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#start_date' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Start-Date_invoice','CRM_Start-Date_start-date-hold','CRM_Start-Date_save'])) {
                    /*** Accept Modal */
                    $content .= '<div id="start_date' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Interview Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="start_date_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="start_date_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<input type="hidden" name="applicant_id_chat" id="applicant_id_chat">';
                    $content .= '<input type="hidden" name="applicant_name_chat" id="applicant_name_chat">';
                    $content .= '<input type="hidden" name="applicant_phone_chat" id="applicant_phone_chat">';
                    $content .= '<textarea name="details" id="start_date_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Rebook_attend')) {
                        $content .= '<button type="submit" name="rebook_attend" value="rebook_attend" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary btn-rounded legitRipple start_date_submit">Attend</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Start-Date_invoice')) {
                        $content .= '<button type="submit" name="start_date_invoice" value="start_date_invoice" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark btn-rounded legitRipple start_date_submit">Invoice</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Start-Date_start-date-hold')) {
                        $content .= '<button type="submit" name="start_date_hold" value="start_date_hold" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-warning btn-rounded legitRipple start_date_submit">Start Date Hold</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Start-Date_save')) {
                        $content .= '<button type="submit" name="start_date_save" value="start_date_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple start_date_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Accept Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })

            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmStartDateHoldCv(){
        return view('administrator.crm.start_date_hold_cv');

    }
    public function crmStartDateHold()
    {
        $auth_user = Auth::user();
        $crm_start_date_hold_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_start_date_hold", "crm_start_date_hold_save"])
            ->whereIn("crm_notes.moved_tab_to", ["start_date_hold", "start_date_hold_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $start_date_hold = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title',     'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date', 'sales.send_cv_limit',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'start_date_hold',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="start_date_hold" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn('histories.sub_stage', ['crm_start_date_hold', 'crm_start_date_hold_save'])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($start_date_hold)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })



            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_start_date_hold_save_note) {
                $content = '';
                if(!empty($crm_start_date_hold_save_note)) {
                    foreach ($crm_start_date_hold_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time;
                            $content .= '</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Start-Date-Hold_revert-start-date','CRM_Start-Date-Hold_save'])) {
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#start_date_hold' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#start_date_hold' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Start-Date-Hold_revert-start-date','CRM_Start-Date-Hold_save'])) {
                    $sent_cv_count = CvNote::where(['sale_id' => $applicant->sale_id, 'status' => 'active'])->count();
                    /*** Accept Modal */
                    $content .= '<div id="start_date_hold' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Interview Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="start_date_hold_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="start_date_hold_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Sent CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$applicant->send_cv_limit.'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="start_date_hold_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    if ($auth_user->hasPermissionTo('CRM_Start-Date-Hold_revert-start-date')) {
                        $content .= '<button type="submit" name="start_date_hold_to_start_date" value="start_date_hold_to_start_date" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark btn-rounded legitRipple start_date_hold_submit">Start Date</button>';
                    }
                    if ($auth_user->hasPermissionTo('CRM_Start-Date-Hold_save')) {
                        $content .= '<button type="submit" name="start_date_hold_save" value="start_date_hold_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple start_date_hold_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Accept Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmInvoiceCv(){
        return view('administrator.crm.invoice_cv');

    }
    public function crmInvoice()
    {
        $auth_user = Auth::user();
        $crm_invoice_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_invoice", "crm_final_save"])
            ->whereIn("crm_notes.moved_tab_to", ["invoice", "final_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $invoices = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'invoice',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="invoice" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn('histories.sub_stage', ['crm_invoice', 'crm_final_save'])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($invoices)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->applicant_postcode.'</a>';
            })




            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_invoice_save_note) {
                $content = '';
                if(!empty($crm_invoice_save_note)) {
                    foreach ($crm_invoice_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<div class="custom-design">';
                            $content .= '<p><strong>DATE:</strong> ' . $crm_save->crm_added_date . ' <strong>TIME:</strong> ' . $crm_save->crm_added_time . '</p>';
                            $content .= '<p><strong>NOTE:</strong> ' . $crm_save->crm_note_details . '</p>';
                            $content .= '</div>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Invoice_paid','CRM_Invoice_dispute','CRM_Invoice_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#invoice' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#invoice' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Invoice_paid','CRM_Invoice_dispute','CRM_Invoice_save'])) {
                    /*** Accept Modal */
                    $content .= '<div id="invoice' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Interview Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="invoice_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="invoice_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="invoice_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Invoice_paid')) {
                        $content .= '<button type="submit" name="invoice_sent" value="invoice_sent" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark btn-rounded legitRipple invoice_submit">Send Invoice</button>';
                    }

//                    if ($auth_user->hasPermissionTo('CRM_Invoice_revert')) {
                    $content .= '<button type="submit" name="revert_invoice" value="revert_invoice" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark btn-rounded legitRipple invoice_submit">Revert Invoice</button>';
//                    }

                    if ($auth_user->hasPermissionTo('CRM_Invoice_dispute')) {
                        $content .= '<button type="submit" name="dispute" value="dispute" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-warning btn-rounded legitRipple invoice_submit">Dispute</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Invoice_save')) {
                        $content .= '<button type="submit" name="final_save" value="final_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success btn-rounded legitRipple invoice_submit">Save</button>';
                    }

                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Accept Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered ">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class=" text-white" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group list-group-flush">';
                $content .= '<li class="list-group-item  "><b>Name:</b> '.$applicant->unit_name.'</li>';
                $content .= '<li class="list-group-item  "><b>Email:</b> '.$applicant->contact_email.'</li>';
                $content .= '<li class="list-group-item "><b>Phone:</b> '.$applicant->contact_phone_number.'</li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn btn-teal" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmInvoiceSentCv(){
//        dd('sda');
        return view('administrator.crm.invoice_sent_cv');

    }
    public function crmInvoiceFinalSent()
    {
        $auth_user = Auth::user();
//        dd($auth_user);
        $crm_invoice_save_note = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->whereIn("histories.sub_stage", ["crm_invoice_sent", "crm_final_save"])
            ->whereIn("crm_notes.moved_tab_to", ["invoice_sent", "final_save"])
            ->select("crm_notes.sale_id", "crm_notes.client_id as app_id", "crm_notes.details as crm_note_details", "crm_notes.crm_added_date", "crm_notes.crm_added_time")
            ->orderBy('crm_notes.id', 'DESC')->get();

        $invoices = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'invoice_sent',
                'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="invoice_sent" and sale_id=sales.id and clients.id=client_id'));
            })->whereIn('histories.sub_stage', ['crm_invoice_sent', 'crm_final_save'])
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($invoices)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })



            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) use ($crm_invoice_save_note) {
                $content = '';
                if(!empty($crm_invoice_save_note)) {
                    foreach ($crm_invoice_save_note as $crm_save) {
                        if (($crm_save->app_id == $applicant->id) && ($crm_save->sale_id == $applicant->sale_id)) {
                            $content .= '<p><b>DATE: </b>';
                            $content .= $crm_save->crm_added_date;
                            $content .= '<b> TIME: </b>';
                            $content .= $crm_save->crm_added_time.'</p>';
                            $content .= '<p><b>NOTE: </b>';
                            $content .= $crm_save->crm_note_details;
                            $content .= '</p>';
                            break;
                        }
                    }
                }
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasAnyPermission(['CRM_Invoice_paid','CRM_Invoice_dispute','CRM_Invoice_save'])) {
                    $content .= '<a href="#" class="dropdown-item sms_action_option"
                                           data-controls-modal="#invoice_sent' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-applicantPhoneJs="' . $applicant->applicant_phone . '"
                                           data-applicantNameJs="' . $applicant->applicant_name . '"
                                           data-applicantIdJs="' . $applicant->id . '"
                                           data-target="#invoice_sent' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Accept </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';

                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasAnyPermission(['CRM_Invoice_paid','CRM_Invoice_dispute','CRM_Invoice_save'])) {
                    /*** Accept Modal */
                    $content .= '<div id="invoice_sent' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade small_msg_modal">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Interview Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="invoice_form_sent' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="invoice_alert_sent' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="invoice_details_sent' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';

                    if ($auth_user->hasPermissionTo('CRM_Invoice_paid')) {
                        $content .= '<button type="submit" name="paid" value="paid" data-app_sale_sent="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success">Paid</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Invoice_dispute')) {
                        $content .= '<button type="submit" name="dispute" value="dispute" data-app_sale_sent="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-dark">Dispute</button>';
                    }

                    if ($auth_user->hasPermissionTo('CRM_Invoice_save')) {
                        $content .= '<button type="submit" name="final_save" value="final_save" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-success">Save</button>';
                    }



                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Accept Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item "><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }

    public function crmDisputeCV(){
        return view('administrator.crm.dispute_cv');

    }
    public function crmDispute()
    {
        $auth_user = Auth::user();
        $dispute = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_notes.crm_added_date', 'crm_notes.crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date', 'sales.send_cv_limit',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'dispute',
                'histories.sub_stage' => 'crm_dispute', 'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="dispute" and sale_id=sales.id and clients.id=client_id'));

            })->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($dispute)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })




            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) {
                $content = '';
                $content .= '<p><b>DATE: </b>';
                $content .= $applicant->crm_added_date;
                $content .= ' <b> TIME: </b>';
                $content .= $applicant->crm_added_time.'</p>';
                $content .= '<p><b>NOTE: </b>';
                $content .= $applicant->details.'</p>';
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';

                if ($auth_user->hasPermissionTo('CRM_Dispute_revert-invoice')) {
                    $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#revert_invoice' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#revert_invoice' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i> Revert </a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item active"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                if ($auth_user->hasPermissionTo('CRM_Dispute_revert-invoice')) {
                    $sent_cv_count = CvNote::where(['sale_id' => $applicant->sale_id, 'status' => 'active'])->count();
                    /*** Revert Invoice Modal */
                    $content .= '<div id="revert_invoice' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade" tabindex="-1">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">Invoice Notes</h5>';
                    $content .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    $content .= '</div>';
                    $content .= '<form action="' . route('processCv') . '" method="POST" id="revert_invoice_form' . $applicant->id . '-' . $applicant->sale_id . '" class="form-horizontal">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="revert_invoice_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="form-group row">';
                    $content .= '<label class="col-form-label col-sm-3">Sent CV</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<label class="col-form-label font-weight-semibold">'.$sent_cv_count.' out of '.$applicant->send_cv_limit.'</label>';
                    $content .= '</div>';
                    $content .= '<label class="col-form-label col-sm-3">Details</label>';
                    $content .= '<div class="col-sm-9">';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '<textarea name="details" id="revert_invoice_details' . $applicant->id . '-' . $applicant->sale_id . '" class="form-control" cols="30" rows="4" placeholder="TYPE HERE.." required></textarea>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="submit" name="dispute_revert_invoice" value="dispute_revert_invoice" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn bg-dark legitRipple revert_invoice_submit"> Invoice </button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';
                    /*** /Revert Invoice Modal */
                }

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }
    public function crmPaidCV(){
        return view('administrator.crm.paid_cv');

    }
    public function crmPaid()
    {
        $auth_user = Auth::user();
        $paid = Client::join('crm_notes', 'clients.id', '=', 'crm_notes.client_id')
            ->join('sales', 'crm_notes.sale_id', '=', 'sales.id')
            ->join('offices', 'sales.head_office', '=', 'offices.id')
            ->join('units', 'sales.head_office_unit', '=', 'units.id')
            ->join('histories', function ($join) {
                $join->on('crm_notes.client_id', '=', 'histories.client_id');
                $join->on('crm_notes.sale_id', '=', 'histories.sale_id');
            })->select('crm_notes.details', 'crm_added_date', 'crm_added_time',
                'clients.id', 'clients.app_name', 'clients.app_postcode', 'clients.app_job_title','clients.app_job_title_prof', 'clients.app_job_category', 'clients.app_phone', 'clients.app_phoneHome', 'clients.paid_status', 'clients.paid_timestamp',
                'sales.id as sale_id', 'sales.job_category as sales_job_category', 'sales.job_title', 'sales.postcode', 'sales.job_type', 'sales.time', 'sales.salary', 'sales.experience', 'sales.qualification', 'sales.benefits', 'sales.posted_date',
                'offices.name as office_name',
                'units.unit_name', 'units.unit_postcode', 'units.contact_name', 'units.contact_phone_number', 'units.contact_landline', 'units.contact_email', 'units.website')
            ->where([
                'clients.app_status' => 'active',
                'crm_notes.moved_tab_to' => 'paid',
                'histories.sub_stage' => 'crm_paid', 'histories.status' => 'active'
            ])->whereIn('crm_notes.id', function($query){
                $query->select(DB::raw('MAX(id) FROM crm_notes WHERE moved_tab_to="paid" and sale_id=sales.id and clients.id=client_id'));
            })
            ->orderBy("crm_notes.created_at","DESC");

        return datatables()->of($paid)
            ->addColumn("name", function ($applicant) {
                $sent_by = CvNote::join('users', 'users.id', '=', 'cv_notes.user_id')
                    ->where('cv_notes.client_id', '=', $applicant->id)
                    ->where('cv_notes.sale_id', '=', $applicant->sale_id)
                    ->first();
                return $sent_by ? $sent_by->fullName : "";
            })
            ->addColumn("applicant_job_title", function ($applicant) {
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
            ->addColumn("applicant_postcode", function ($applicant) {
                if ($applicant->paid_status == 'close')
                    return $applicant->app_postcode;
                else
                    return '<a href="'.route('15kmrange', $applicant->id).'" class="btn-link legitRipple">'.$applicant->app_postcode.'</a>';
            })

            ->addColumn("job_details", function ($applicant) {
                $content = '';
                $content .= '<a href="#" data-controls-modal="#job_details'.$applicant->id.'-'.$applicant->sale_id.'"
                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                    data-target="#job_details'.$applicant->id.'-'.$applicant->sale_id.'">Details</a>';
                $content .= '<div id="job_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-scrollable modal-lg">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">'.$applicant->app_name.'\'s Job Details</h5>';
                $content .= '<button type="button" class="close text-light" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<div class="container">';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Job Title:</h6>';
                $content .= '<p>'.$applicant->job_title.'</p>';
                $content .= '<h6 class="font-weight-bold">Postcode:</h6>';
                $content .= '<p>'.$applicant->postcode.'</p>';
                $content .= '<h6 class="font-weight-bold">Job Type:</h6>';
                $content .= '<p>'.$applicant->job_type.'</p>';
                $content .= '<h6 class="font-weight-bold">Timings:</h6>';
                $content .= '<p>'.$applicant->time.'</p>';
                $content .= '</div>';
                $content .= '<div class="col-md-6">';
                $content .= '<h6 class="font-weight-bold">Salary:</h6>';
                $content .= '<p>'.$applicant->salary.'</p>';
                $content .= '<h6 class="font-weight-bold">Experience:</h6>';
                $content .= '<p>'.$applicant->experience.'</p>';
                $content .= '<h6 class="font-weight-bold">Qualification:</h6>';
                $content .= '<p>'.$applicant->qualification.'</p>';
                $content .= '<h6 class="font-weight-bold">Benefits:</h6>';
                $content .= '<p>'.$applicant->benefits.'</p>';
                $content .= '<h6 class="font-weight-bold">Posted Date:</h6>';
                $content .= '<p>'.$applicant->posted_date.'</p>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-primary text-light" data-dismiss="modal">Close</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                return $content;
            })
            ->addColumn("crm_note", function ($applicant) {
                $content = '';
                $content .= '<p><b>DATE: </b>';
                $content .= $applicant->crm_added_date;
                $content .= ' <b> TIME: </b>';
                $content .= $applicant->crm_added_time.'</p>';
                $content .= '<p><b>NOTE: </b>';
                $content .= $applicant->details.'</p>';
                return $content;
            })
            ->addColumn("action", function ($applicant) use ($auth_user) {
                $content = '';
                /*** action menu */
                $content .= '<div class="btn-group">';
                $content .= '<div class="dropdown">';
                $content .= '<a href="#" class="list-icons-item" data-toggle="dropdown">';
                $content .= '<i class="bi bi-list"></i>';
                $content .= '</a>';
                $content .= '<div class="dropdown-menu dropdown-menu-right">';
                if ($auth_user->hasPermissionTo('CRM_Paid_open-close-cv')) {
                    $paid_status_button = ($applicant->paid_status == 'close') ? 'Open' : 'Close';
                    $content .= '<a href="#" class="dropdown-item"
                       data-controls-modal="#paid_status' . $applicant->id . '-' . $applicant->sale_id . '" data-backdrop="static"
                       data-keyboard="false" data-toggle="modal"
                       data-target="#paid_status' . $applicant->id . '-' . $applicant->sale_id . '">';
                    $content .= '<i class="icon-file-confirm"></i>' . $paid_status_button . '</a>';
                }
                $content .= '<a href="#" class="dropdown-item"
                                           data-controls-modal="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'" data-backdrop="static"
                                           data-keyboard="false" data-toggle="modal"
                                           data-target="#manager_details'.$applicant->id.'-'.$applicant->sale_id.'">';
                $content .= '<i class="icon-file-confirm"></i>Manager</a>';
                $content .= '<a href="'.route('viewAllCrmNotes',["crm_applicant_id" => $applicant->id,"crm_sale_id" => $applicant->sale_id]).'" class="dropdown-item">';
                $content .= '<i class="icon-file-confirm"></i>View All Notes</a>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';

                if ($auth_user->hasPermissionTo('CRM_Paid_open-close-cv')) {
                    /*** Paid Status Modal */
                    $content .= '<div id="paid_status' . $applicant->id . '-' . $applicant->sale_id . '" class="modal fade">';
                    $content .= '<div class="modal-dialog modal-lg">';
                    $content .= '<div class="modal-content">';
                    $content .= '<div class="modal-header">';
                    $content .= '<h5 class="modal-title">' . $paid_status_button . ' ' . $applicant->app_name . '\'s CV</h5>';
                    $content .= '<button type="button" class="btn-close" data-dismiss="modal"></button>';
                    $content .= '</div>';
                    $content .= '<form method="POST" id="paid_status_form' . $applicant->id . '-' . $applicant->sale_id . '" class="modal-form">';
                    $content .= csrf_field();
                    $content .= '<div class="modal-body">';
                    $content .= '<div id="paid_status_alert' . $applicant->id . '-' . $applicant->sale_id . '"></div>';
                    $content .= '<div class="mb-3">';
                    $current_paid_status = ($paid_status_button == 'Open') ? 'closed' : 'opened';
                    $paid_status_timestamp = Carbon::parse($applicant->paid_timestamp);
                    $content .= '<label class="form-label">Client CV has been ' . $current_paid_status . ' since ' . $paid_status_timestamp->format('jS F Y') . ' (' . $paid_status_timestamp->diff(Carbon::now())->format('%y years, %m months and %d days') . '). Are you sure you want to ' . $paid_status_button . ' it?</label>';
                    $content .= '<input type="hidden" name="app_hidden_id" value="' . $applicant->id . '">';
                    $content .= '<input type="hidden" name="job_hidden_id" value="' . $applicant->sale_id . '">';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="modal-footer">';
                    $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
                    $content .= '<button type="submit" name="paid_status" value="' . $paid_status_button . '" data-app_sale="' . $applicant->id . '-' . $applicant->sale_id . '" class="btn btn-primary paid_status_submit"> ' . $paid_status_button . ' </button>';
                    $content .= '</div>';
                    $content .= '</form>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '</div>';

                    /*** /Paid Status Modal */
                }

                /*** Manager Details Modal */
                $content .= '<div id="manager_details'.$applicant->id.'-'.$applicant->sale_id.'" class="modal fade" tabindex="-1">';
                $content .= '<div class="modal-dialog modal-dialog-centered">';
                $content .= '<div class="modal-content">';
                $content .= '<div class="modal-header bg-primary text-light">';
                $content .= '<h5 class="modal-title">Manager Details</h5>';
                $content .= '<button type="button" class="" data-dismiss="modal">&times;</button>';
                $content .= '</div>';
                $content .= '<div class="modal-body">';
                $content .= '<ul class="list-group ">';
                $content .= '<li class="list-group-item"><p><b>Name: </b>'.$applicant->unit_name.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Email: </b>'.$applicant->contact_email.'</p></li>';
                $content .= '<li class="list-group-item"><p><b>Phone: </b>'.$applicant->contact_phone_number.'</p></li>';
                $content .= '</ul>';
                $content .= '</div>';
                $content .= '<div class="modal-footer">';
                $content .= '<button type="button" class="btn bg-teal legitRipple" data-dismiss="modal">CLOSE</button>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                /*** /Manager Details Modal */

                return $content;
            })
            ->rawColumns(['name','applicant_job_title','applicant_postcode','job_details','crm_note','action','office_name'])
            ->make(true);
    }

    public function store(Request $request)
    {

        date_default_timezone_set('Europe/London');
        // request_to_confirm
        // interview_rebook
        // ajax call code

//dd($request->all());
        $cv_sent_reject_value = $request->Input('cv_sent_reject');
        $cv_sent_request_value = $request->Input('cv_sent_request');
        $cv_sent_save_value = $request->Input('cv_sent_save');
        $audit_data['action'] = '';
        $client_id = $request->Input('app_hidden_id');
//         dd($request->all());
        // echo $cv_sent_reject_value.' test';exit();
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';




        // if($client_id==12199 && $sale_id==8937)


        if (!empty($cv_sent_save_value) && ($cv_sent_save_value == 'cv_sent_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_saved";
            $crm_notes->save();
//            dd($client_id,$request->all());

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
                        </div>';
                    echo $html;
                }

            } else {
                echo $html;
            }
        } elseif (!empty($cv_sent_request_value) && ($cv_sent_request_value == 'cv_sent_request')) {
//            dd($audit_data['action'],$client_id);
//dd($request->all());
            $audit_data['action'] = "Request";
            Client::where("id", $client_id)->update(['is_in_crm_request' => 1, 'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time =Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_request";
            $crm_notes->status = "active";
            $crm_notes->save();
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id, "moved_tab_to" => "cleared"])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_request';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Request successfully
                        </div>';
                    echo $html;
                }
            } else {
                echo $html;
            }
        } elseif (!empty($cv_sent_reject_value) && ($cv_sent_reject_value == 'cv_sent_reject')) {

            $audit_data['action'] = "Reject";
//            dd($audit_data['action'],'reject');

            $audit_data['reject_reason'] = $reject_reason = $request->Input('reject_reason');
//            dd($audit_data['reject_reason'],,);
            $client_id=$request->app_hidden_id;
            Client::where("id", $client_id)->update(['is_in_crm_reject' => 1,
                'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $crm_notes->status = 'active';
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_reject";
            $crm_notes->save();



            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                $crm_rejected_cv = new Crm_rejected_cv();
                $crm_rejected_cv->client_id = $client_id;
                $crm_rejected_cv->sale_id = $sale_id;
                $crm_rejected_cv->user_id = $auth_user;
                $crm_rejected_cv->crm_note_id = $last_inserted_note;
                $crm_rejected_cv->reason = $reject_reason;
                $crm_rejected_cv->status = 'active';
                $crm_rejected_cv->crm_rejected_cv_note = $details;
                $crm_rejected_cv->crm_rejected_cv_date = Carbon::now()->format("Y-m-d");
                $crm_rejected_cv->crm_rejected_cv_time = Carbon::now()->format("h:i:s");
                $crm_rejected_cv->save();
                $last_crm_reject_id = $crm_rejected_cv->id;
                $crm_last_insert_id = md5($last_crm_reject_id);
//                Crm_rejected_cv::where("id", $last_crm_reject_id)->update(['crm_rejected_cv_uid' => $crm_last_insert_id]);
                CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_reject';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV rejected successfully
                        </div>';
                    echo $html;
                }
            } else {
                echo $html;
            }
        }








//dd($request->all());

        // simple call code
        /*** Sent CVs tab */
        $cv_sent_reject_value = $request->Input('cv_sent_reject');
        $cv_sent_request_value = $request->Input('cv_sent_request');
        $cv_sent_save_value = $request->Input('cv_sent_save');
// echo $request->Input('cv_sent_request');exit();
        /*** Rejected CV tab */
        $rejected_cv_revert_sent_cvs_value = $request->Input('rejected_cv_revert_sent_cvs');
        /*** Request tab */
        $request_reject = $request->Input('request_reject');
        $request_to_confirm = $request->Input('request_to_confirm');
        $request_to_save = $request->Input('request_to_save');

        /*** rebook to confrime By Request tab */
        $rebook_confirm=$request->input('rebook_confirm');
//        dd($rebook_confirm);
        /*** Rejected By Request tab */
        $rejected_request_revert_to_sent_cvs = $request->input('rejected_request_revert_sent_cvs');
        $rejected_request_revert_to_request = $request->input('rejected_request_revert_to_request');
//        dd($rejected_request_revert_to_sent_cvs,$request->rejected_request_revert_sent_cvs);

        /*** Interview Confirmation tab */
        $interview_not_attend = $request->Input('interview_not_attend');
        $interview_attend = $request->Input('interview_attend');
        $interview_save = $request->Input('interview_save');
        $rebook = $request->Input('interview_rebook');
        $confirm_revert_request = $request->Input('confirm_revert_request');

        /*** Not Attended tab */
        $revert_to_attend = $request->Input('back_to_attended');

        /*** Attended tab */
        $start_date = $request->Input('start_date');
        $prestart_save = $request->Input('prestart_save');

        /*** Start Date tab */
        $start_date_invoice = $request->Input('start_date_invoice');
        $start_date_hold = $request->Input('start_date_hold');
        $start_date_save = $request->Input('start_date_save');

        /*** Start Date Hold tab */
        $start_date_hold_to_start_date = $request->Input('start_date_hold_to_start_date');
        $start_date_hold_save = $request->Input('start_date_hold_save');

        /*** Invoice tab */
        $paid = $request->Input('paid');
        $dispute = $request->Input('dispute');
        $final_save = $request->Input('final_save');

        $audit_data['action'] = '';
        $client_id = $request->Input('app_hidden_id');
//         dd($client_id,$request->all());
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        if (!empty($cv_sent_save_value)) {
//            dd(' not sent cv');
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_saved";
            $crm_notes->status = 'active';
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }

            } else {
                return redirect()->back();
            }
        } elseif (!empty($cv_sent_request_value)) {
//            dd('request');
            $audit_data['action'] = "Request";
            Client::where("id", $client_id)->update(['is_in_crm_request' =>1, 'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_request";
            $crm_notes->status = 'active';
            $crm_notes->save();

            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id, "moved_tab_to" => "cleared"])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();

                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($cv_sent_reject_value)) {
//            dd('reject',$request->all());
            $client_id=$request->app_hidden_id;
            $audit_data['action'] = "Reject";
            $audit_data['reject_reason'] = $reject_reason = $request->Input('reject_reason');
            Client::where("id", $client_id)->update(['is_in_crm_reject' => 1,
                'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $crm_notes->status = 'disable';
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_reject";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
                $crm_rejected_cv = new Crm_rejected_cv();
                $crm_rejected_cv->client_id = $client_id;
                $crm_rejected_cv->sale_id = $sale_id;
                $crm_rejected_cv->user_id = $auth_user;
                $crm_rejected_cv->crm_note_id = $last_inserted_note;
                $crm_rejected_cv->reason = $reject_reason;
                $crm_rejected_cv->crm_rejected_cv_note = $details;
                $crm_rejected_cv->crm_rejected_cv_date = Carbon::now()->format("Y-m-d");
                $crm_rejected_cv->crm_rejected_cv_time = Carbon::now()->format("H:i:s");
                $crm_rejected_cv->save();
                $last_crm_reject_id = $crm_rejected_cv->id;
                $crm_last_insert_id = md5($last_crm_reject_id);
//                Crm_rejected_cv::where("id", $last_crm_reject_id)->update(['crm_rejected_cv_uid' => $crm_last_insert_id]);
                CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_reject';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($rejected_cv_revert_sent_cvs_value)) {
            //back to revert sent cv data
//            dd($client_id);

            $crm_note_id = CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id, 'moved_tab_to' => 'cv_sent_reject'])->select('id')->latest()->first()->id;
            Crm_rejected_cv::where(["client_id" => $client_id,"sale_id" => $sale_id, 'crm_note_id' => $crm_note_id])->update(["status" => "disable"]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved"])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent";
            $crm_notes->status = 'active';
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Rejected CV revert to Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($request_reject)) {
//            dd($audit_data['action'],$client_id);
            $audit_data['action'] = "Reject";
            Client::where("id", $client_id)->update(['is_in_crm_request_reject' => 1, 'is_in_crm_request' => 0]);
//            Interview::where(["client_id" => $client_id, "sale_id" => $sale_id])->update(['status' => 'disable']);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "request_reject";
            $crm_notes->status = 'active';
            $crm_notes->save();
//            CrmNote::where(["client_id" => $client_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Request');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request_reject';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($request_to_confirm)) {
            $audit_data['action'] = "Confirm";
            Client::where("id", $client_id)->update(['is_crm_request_confirm' => 1, 'is_in_crm_request' => 0, 'is_in_crm_request_reject' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "request_confirm";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Request');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request_confirm';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($request_to_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $crm_notes->status = 'active';
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "request_save";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Request');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
//                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($rejected_request_revert_to_sent_cvs)) {
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id, "moved_tab_to" => "cleared"])->update(["status" => "active"]);
            CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved", "cv_sent_request"])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent";
            $crm_notes->status = 'active';
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Rejected CV revert to Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($rejected_request_revert_to_request)) {
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_request";
            $crm_notes->status = 'active';
            $crm_notes->save();
//            CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved"])->update(["status" => "active"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Request');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($interview_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "interview_save";
            $crm_notes->status = 'active';
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_interview_save';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif(!empty($confirm_revert_request)) {
            $audit_data['action'] = "Confirmation Revert Request";
            Interview::where(['client_id' => $client_id, 'sale_id' => $sale_id, 'status' => 'active'])->update(['status' => 'disable']);
            CrmNote::where(['client_id' => $client_id, 'sale_id' => $sale_id])
                ->whereIn('moved_tab_to', ['cv_sent_request', 'request_to_save', 'request_to_confirm', 'interview_save'])
                ->update(['status' => 'disable']);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_request";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation Revert Request');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } elseif (!empty($interview_attend)) {
            $audit_data['action'] = "Attend";
            Client::where("id", $client_id)->update(['is_crm_interview_attended' => 1, 'is_crm_request_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "interview_attended";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
//                    "user_id" => $auth_user,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->sub_stage = 'crm_interview_attended';
                $history->status = 'active';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($interview_not_attend)) {
            $audit_data['action'] = "Not Attend";
            Client::where("id", $client_id)->update(['is_crm_interview_attended' =>0, 'is_crm_request_confirm' => 0]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "interview_not_attended";
            $crm_notes->save();
            CrmNote::where(["client_id" => $client_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_interview_not_attended';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($revert_to_attend)) {
            $audit_data['action'] = "Revert To Attend";
            Client::where("id", $client_id)->update(['is_crm_interview_attended' => 1]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "interview_attended";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Not Attended');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_interview_attended';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date)) {
            $audit_data['action'] = "Start Date";
            Client::where("id", $client_id)->update(['is_in_crm_start_date' => 1, 'is_crm_interview_attended' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Attended To Pre-Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_start_date';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($prestart_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "prestart_save";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Attended To Pre-Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_prestart_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date_invoice)) {
            $audit_data['action'] = "Invoice";
            Client::where("id", $client_id)->update(['is_in_crm_invoice' => 1, 'is_in_crm_start_date' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "invoice";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
                CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_invoice';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date_hold)) {
            $audit_data['action'] = "Start Date Hold";
            Client::where("id", $client_id)->update(['is_in_crm_start_date_hold' =>1, 'is_in_crm_start_date' => 0]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_hold";
            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $client_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_start_date_hold';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_save";
            $crm_notes->status = "active";

            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Cr::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_start_date_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date_hold_to_start_date)) {
            $audit_data['action'] = "Start Date";
            Client::where("id", $client_id)->update(['is_in_crm_start_date_hold' => 0, 'is_in_crm_start_date' => 1]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
//            Quality_notes::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "active"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_back";
            $crm_notes->status = "active";

            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date Hold');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                CrmNote::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_start_date_back';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($start_date_hold_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_hold_save";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date Hold');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_start_date_hold_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($paid)) {
            $audit_data['action'] = "Paid";
            Client::where("id", $client_id)->update(['is_in_crm_paid' => 1, 'is_in_crm_invoice' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "paid";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "paid"]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_paid';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($rebook)) {
//            dd('rebook');
            $audit_data['action'] = "rebook";
//            Client::where("id", $client_id)->update(['is_in_crm_paid' => 1, 'is_in_crm_invoice' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "rebook";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                CrmNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "paid"]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_rebook';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($dispute)) {
            $audit_data['action'] = "Dispute";
            Client::where("id", $client_id)->update(['is_in_crm_dispute' => 1, 'is_in_crm_invoice' => 0]);
            CvNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $client_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "dispute";
            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $client_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_dispute';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
        elseif (!empty($final_save)) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $client_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "final_save";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                $crm_note_uid = md5($last_inserted_note);
//                Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
                History::where([
                    "client_id" => $client_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $client_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_final_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $history_uid = md5($last_inserted_history);
//                    History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        }
    }

    public function getInterviewSchedule(Request $request)
    {
//dd($request->all());
        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';
        $audit_data['action'] = "Schedule Interview";
        $interview_scheduled = 0;
        $user = Auth::user()->id;
        $interview = new Interview();
        $interview->user_id = $user;
        $interview->sale_id = $request->Input('sale_id');
        $interview->client_id = $request->Input('applicant_id');
        $interview->schedule_date = $request->Input('schedule_date');
        $interview->schedule_time = $request->Input('schedule_time');
        $interview->status = 'active';
        $interview->save();

        /*** activity log
        $this->action_observer->action($audit_data, 'CRM > Request');
         */
        $last_inserted_interview = $interview->id;
        if ($last_inserted_interview > 0) {
            $interview_uid = md5($last_inserted_interview);
            toastr()->success('Interview scheduled successfully');
            return redirect()->back();
        } else {
            toastr()->success('WHOOPS! Something Went Wrong!!');
            return redirect()->back();
        }
    }

}
