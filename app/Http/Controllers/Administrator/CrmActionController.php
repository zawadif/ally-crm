<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Crm_rejected_cv;
use App\Models\CrmNote;
use App\Models\CvNote;
use App\Models\History;
use App\Models\QualityNote;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmActionController extends Controller
{
    public function rebookAction(Request $request)
    {
        date_default_timezone_set('Europe/London');
//dd($request->all());
        /*** Interview Confirmation tab */
        $form_action = $request->Input('form_action');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        if (!empty($form_action) && ($form_action == 'rebook_confirm')) {
//            dd($form_action);
            $audit_data['action'] = "Confirm";
            Client::where("id", $applicant_id)->update(['is_crm_request_confirm' => 1, 'is_in_crm_request' => 0, 'is_in_crm_request_reject' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_request_confirm';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){


                    return response()->json(['success' => true, 'message' => 'Client CV Revert In Confirmation Successfully']);

                }
            } else {
                toastr()->error('Not Applicant CV Revert In Confirmation Successfully!');

                return response()->json(['success' => false, 'message' => 'Not Applicant CV Revert In Confirmation Successfully']);

            }
        }elseif (!empty($form_action) && ($form_action == 'rebook_not_attend')) {
            $audit_data['action'] = "Not Attend";
            Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 0, 'is_crm_request_confirm' => 0]);
            CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "interview_not_attended";
//            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_interview_not_attended';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Not Attended Successfully
						</div>';
//                    toastr()->success('Client CV moved to Not Attended Successfully');

                    return response()->json(['success' => true, 'message' => 'Client CV moved to Not Attended Successfully']);

                }
            } else {
//                toastr()->success('Not Client CV moved to Not Attended Successfully');
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Not Attended Successfully']);

            }
        } elseif (!empty($form_action) && ($form_action == 'rebook_attend')) {
            $audit_data['action'] = "Attend";
            Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 1, 'is_crm_request_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                History::where([
                    "client_id" => $applicant_id,
//                    "user_id" => $auth_user,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Attended Successfully
						</div>';
                    toastr()->success('Applicant CV moved to Attended Successfully');
                    return response()->json(['success' => true, 'message' => 'Client marked as not attended']);

                }
            } else {
                toastr()->error('Not Applicant CV moved to Attended Successfully');
                return response()->json(['success' => false, 'message' => 'Client marked as not attended']);

            }
        } elseif (!empty($form_action) && ($form_action == 'rebook_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "rebook_save";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_rebook_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
						</div>';
//                    toastr()->success('Note saved Successfully');
                    return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                }
            } else {
//                toastr()->error('Not Note saved Successfully!');
                return response()->json(['success' => false, 'message' => 'Not Note saved Successfully']);


            }
        }
    }
    public function attendedToPreStartAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Attended tab */
        $start_date = $request->Input('start_date');
        $prestart_save = $request->Input('prestart_save');
        $decline = $request->Input('decline');
        $interview_rebook = $request->Input('Confirmation_rebook');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        if (!empty($interview_rebook) && ($interview_rebook == 'Confirmation_rebook')) {
            $audit_data['action'] = "Rebook";

            Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 1, 'is_crm_request_confirm' =>0]);
            // Crm_rejected_cv::where('applicant_id',$applicant_id)->delete();
            CrmNote::where([
                "client_id" => $applicant_id,
                "sale_id" => $sale_id,
                "moved_tab_to"=> "rebook"
            ])->delete();
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "rebook";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    //                    "user_id" => $auth_user,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_rebook';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Rebook Successfully
                                </div>';
                    return response()->json(['success' => true, 'message' => 'Client CV moved to Rebook Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Rebook Successfully']);

            }
        } elseif (!empty($start_date) && ($start_date == 'start_date')) {
            $audit_data['action'] = "Start Date";
            Client::where("id", $applicant_id)->update(['is_in_crm_start_date' => 1, 'is_crm_interview_attended' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Attended To Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Start Date Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Client CV moved to Start Date Successfully']);

//                    echo $html;
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Start Date Successfully']);

            }
        } elseif (!empty($prestart_save) && ($prestart_save == 'prestart_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sales_id = $sale_id;
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
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                }
            } else {
                echo $html;
            }
        } elseif (!empty($decline) && ($decline == 'decline')) {
            $audit_data['action'] = "Declined";
            Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 0, 'is_crm_request_confirm' => 0]);
            CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "declined";
            $crm_notes->save();
            CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Confirmation');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_declined';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Declined Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Client CV moved to Declined Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Client CV moved to Declined Successfully']);

            }
        }
    }
    public function declinedAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Declined tab */
        $declined_revert_attended = $request->Input('declined_revert_attended');

        $audit_data['action'] = 'Declined revert Attended';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        $sale = Sale::find($sale_id);
        if ($sale) {
            $sent_cv_count = CvNote::where(['sale_id' => $sale_id, 'status' => 'active'])->count();
            if ($sent_cv_count < $sale->send_cv_limit) {
                if (!empty($declined_revert_attended) && ($declined_revert_attended == 'declined_revert_attended')) {
                    $audit_data['action'] = "Revert To Attend";
                    Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 1]);
                    CvNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "active"]);

                    /*** latest sent cv records */
                    $crm_notes_index = 0;
                    $latest_sent_cv = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "cv_sent")->latest()->first();
                    $all_cv_sent_saved = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "cv_sent_saved")
                        ->where('created_at', '>=', $latest_sent_cv->created_at)->get();
                    $crm_notes_ids[$crm_notes_index++] = $latest_sent_cv->id;
                    foreach ($all_cv_sent_saved as $cv) {
                        $crm_notes_ids[$crm_notes_index++] = $cv->id;
                    }
                    /*** latest request records */
                    $latest_request = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "cv_sent_request")->latest()->first();
                    $all_request_saved = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "request_save")
                        ->where('created_at', '>=', $latest_request->created_at)->get();
                    $crm_notes_ids[$crm_notes_index++] = $latest_request->id;
                    foreach ($all_request_saved as $cv) {
                        $crm_notes_ids[$crm_notes_index++] = $cv->id;
                    }
                    /*** latest confirmation records */
                    $latest_confirmation = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "request_confirm")->latest()->first();
                    $all_confirmation_saved = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "interview_save")
                        ->where('created_at', '>=', $latest_confirmation->created_at)->get();
                    $crm_notes_ids[$crm_notes_index++] = $latest_confirmation->id;
                    foreach ($all_confirmation_saved as $cv) {
                        $crm_notes_ids[$crm_notes_index++] = $cv->id;
                    }
                    /*** latest rebook records */
                    $latest_rebook = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "rebook")->latest()->first();
                    if ($latest_rebook) {
                        $all_rebook_saved = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "rebook_save")
                            ->where('created_at', '>=', $latest_rebook->created_at)->get();
                        $crm_notes_ids[$crm_notes_index++] = $latest_rebook->id;
                        foreach ($all_rebook_saved as $cv) {
                            $crm_notes_ids[$crm_notes_index++] = $cv->id;
                        }
                    }
                    CrmNote::whereIn('id', $crm_notes_ids)->update(["status" => "active"]);
//                        Crm_note::where(["applicant_id" => $applicant_id, "sales_id" => $sale_id])->whereIn('moved_tab_to', ['cv_sent', 'cv_sent_saved', 'cv_sent_request', 'request_save', 'request_confirm', 'prestart_save', 'interview_attended', 'interview_save', 'rebook'])->update(["status" => "active"]);
                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "interview_attended";
                    $crm_notes->status = "active";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Attendd');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                       History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
                        $history->user_id = $auth_user;
                        $history->sale_id = $sale_id;
                        $history->stage = 'crm';
                        $history->status = 'active';
                        $history->sub_stage = 'crm_interview_attended';
                        $history->history_added_date = Carbon::now()->format("Y-m-d");
                        $history->history_added_time = Carbon::now()->format("H:i:s");
                        $history->save();
                        $last_inserted_history = $history->id;
                        if ($last_inserted_history > 0) {
                            $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">' . $request->input('module') . '</span> Applicant CV reverted Attended to Pre-Start Date Successfully
						</div>';
                            return response()->json(['success' => true, 'message' => 'Client  CV reverted Attended to Pre-Start Date Successfully']);

                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Not Client  CV reverted Attended to Pre-Start Date Successfully']);

                    }
                }
            } else {
                return response()->json(['success' => false, 'message' => 'You cannot perform this action. Send CV Limit for this Sale has reached maximum!!']);


            }
        } else {
            return response()->json(['success' => false, 'message' => 'WHOOPS! Sale not found!!']);

        }
    }
    public function startDateAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Start Date tab */
        $start_date_invoice = $request->Input('start_date_invoice');
        $start_date_hold = $request->Input('start_date_hold');
        $start_date_save = $request->Input('start_date_save');
        $attended = $request->Input('rebook_attend');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        if (!empty($attended) && ($attended == 'rebook_attend')) {
            $audit_data['action'] = "Attend";
            Client::where("id", $applicant_id)->update(['is_crm_interview_attended' => 1, 'is_crm_request_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                History::where([
                    "client_id" => $applicant_id,
                    //                    "user_id" => $auth_user,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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
                    return response()->json(['success' => true, 'message' => 'Client CV moved to Attended Successfully']);


                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Attended Successfully!']);

            }
        } elseif (!empty($start_date) && ($start_date == 'start_date')) {
            $audit_data['action'] = "Start Date";
            Client::where("id", $applicant_id)->update(['is_in_crm_start_date' => 1, 'is_crm_interview_attended' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Attended To Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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

                    return response()->json(['success' => true, 'message' => 'Client CV moved to Start Date Successfully!']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Start Date Successfully!']);

            }
        } elseif (!empty($start_date_invoice) && ($start_date_invoice == 'start_date_invoice')) {
            $audit_data['action'] = "Invoice";
            Client::where("id", $applicant_id)->update(['is_in_crm_invoice' =>1, 'is_in_crm_start_date' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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

                    return response()->json(['success' => true, 'message' => 'Client CV moved to Invoice Successfully!']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Invoice Successfully!']);

            }
        } elseif (!empty($start_date_hold) && ($start_date_hold == 'start_date_hold')) {
            $audit_data['action'] = "Start Date Hold";
            Client::where("id", $applicant_id)->update(['is_in_crm_start_date_hold' => 1, 'is_in_crm_start_date' => 0]);
            CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_hold";
            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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

                    return response()->json(['success' => true, 'message' => 'Client CV moved to Start Date Hold Successfully!']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Start Date Hold Successfully!']);

            }
        } elseif (!empty($start_date_save) && ($start_date_save == 'start_date_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date_save";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
               History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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

                    return response()->json(['success' => true, 'message' => 'Note saved Successfully!']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Note saved Successfully!']);

            }
        }
    }
    public function startDateHoldAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Start Date Hold tab */
        $start_date_hold_to_start_date = $request->Input('start_date_hold_to_start_date');
        $start_date_hold_save = $request->Input('start_date_hold_save');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        $sale = Sale::find($sale_id);
        if ($sale) {
            if (!empty($start_date_hold_to_start_date) && ($start_date_hold_to_start_date == 'start_date_hold_to_start_date')) {
                $sent_cv_count = CvNote::where(['sale_id' => $sale_id, 'status' => 'active'])->count();
                if ($sent_cv_count < $sale->send_cv_limit) {
                    $audit_data['action'] = "Start Date";
                    Client::where("id", $applicant_id)->update(['is_in_crm_start_date_hold' => 0, 'is_in_crm_start_date' => 1]);
                    CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "active"]);
//            Quality_notes::where(["applicant_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "active"]);
                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "start_date_back";
                    $crm_notes->status = "active";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Start Date Hold');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                         History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
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

                            return response()->json(['success' => true, 'message' => 'Client  CV reverted Start Date Successfully']);

                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Not Client  CV reverted Start Date Successfully']);

                    }
                } else {

                    return response()->json(['success' => false, 'message' => 'You cannot perform this action. Send CV Limit for this Sale has reached maximum!!']);

                }
            } elseif (!empty($start_date_hold_save) && ($start_date_hold_save == 'start_date_hold_save')) {
                $audit_data['action'] = "Save";
                $crm_notes = new CrmNote();
                $crm_notes->client_id = $applicant_id;
                $crm_notes->user_id = $auth_user;
                $crm_notes->sale_id = $sale_id;
                $crm_notes->details = $details;
                $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                $crm_notes->moved_tab_to = "start_date_hold_save";
                $crm_notes->status = "active";
                $crm_notes->save();

                /*** activity log
                 * $this->action_observer->action($audit_data, 'CRM > Start Date Hold');
                 */

                $last_inserted_note = $crm_notes->id;
                if ($last_inserted_note > 0) {
                    History::where([
                        "client_id" => $applicant_id,
                        "sale_id" => $sale_id
                    ])->update(["status" => "disable"]);
                    $history = new History();
                    $history->client_id = $applicant_id;
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

                        return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Not Note saved Successfully']);

                }
            }
        } else {

            return response()->json(['success' => false, 'message' => 'Sale not found!!']);

        }
    }

    public function invoiceAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Invoice tab */
        $paid = $request->Input('paid');
        $invoice_sent = $request->Input('invoice_sent');
        $invoice_revert = $request->Input('revert_invoice');
        $dispute = $request->Input('dispute');
        $final_save = $request->Input('final_save');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';
        // THIS IS MANUAL FUNCTION TO REVERT CANDIDATE FROM INVOICE TO START DATE

        //     if (!empty($final_save) && ($final_save == 'final_save')) {

        //         if($applicant_id==12199 && $sale_id==8937)
        //         {
        //         $audit_data['action'] = "Start Date";
        //         Applicant::where("id", $applicant_id)->update(['is_in_crm_start_date' => 'yes','is_in_crm_invoice' => 'no','is_crm_interview_attended' => 'pending']);
        //         $crm_notes = new Crm_note();
        //         $crm_notes->applicant_id = $applicant_id;
        //         $crm_notes->user_id = $auth_user;
        //         $crm_notes->sales_id = $sale_id;
        //         $crm_notes->details = $details;
        //         $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d")
        //         $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
        //         $crm_notes->moved_tab_to = "start_date";
        //         $crm_notes->save();

        //         /*** activity log
        //         $this->action_observer->action($audit_data, 'CRM > Attended To Pre-Start Date');
        //          */

        //         $last_inserted_note = $crm_notes->id;
        //         if ($last_inserted_note > 0) {
        //             $crm_note_uid = md5($last_inserted_note);
        //             Crm_note::where('id', $last_inserted_note)->update(['crm_notes_uid' => $crm_note_uid]);
        //             History::where([
        //                 "applicant_id" => $applicant_id,
        //                 "sale_id" => $sale_id
        //             ])->update(["status" => "disable"]);
        //             $history = new History();
        //             $history->applicant_id = $applicant_id;
        //             $history->user_id = $auth_user;
        //             $history->sale_id = $sale_id;
        //             $history->stage = 'crm';
        //             $history->sub_stage = 'crm_start_date';
        //             $history->history_added_date = Carbon::now()->format("Y-m-d")
        //             $history->history_added_time = Carbon::now()->format("H:i:s");
        //             $history->save();
        //             $last_inserted_history = $history->id;
        //             if($last_inserted_history > 0){
        //                 $history_uid = md5($last_inserted_history);
        //                 History::where('id', $last_inserted_history)->update(['history_uid' => $history_uid]);
        //                 return redirect()->route('index');
        //             }
        //         } else {
        //             return redirect()->route('index');
        //         }
        //     }
        // }

        if (!empty($paid) && ($paid == 'paid')) {
            $audit_data['action'] = "Paid";
            Client::where("id", $applicant_id)->update(['is_in_crm_paid' => 1, 'is_in_crm_invoice' => 0,'is_in_crm_invoice_sent' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "paid"]);
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->applicant_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_paid';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();

//                $dispatcher = Applicant::getEventDispatcher();
//                Applicant::unsetEventDispatcher();
                $update_columns = ['paid_status' => 'close', 'paid_timestamp' => Carbon::now()];
                $update_applicant = Client::where('id', '=', $applicant_id)->update($update_columns);
//                Applicant::setEventDispatcher($dispatcher);

//                if ($update_applicant) {
//                    /*** activity log */
//                    $action_observer = new ActionObserver();
//                    $action_observer->changeCvStatus($applicant_id, $update_columns, 'Closed');
//                }

                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){

                    return response()->json(['success' => true, 'message' => 'Client CV moved to Paid Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Paid Successfully']);

            }
        }elseif (!empty($invoice_sent) && ($invoice_sent == 'invoice_sent')) {
            $audit_data['action'] = "invoice_sent";
            Client::where("id", $applicant_id)->update(['is_in_crm_invoice' => 0, 'is_in_crm_invoice_sent' =>1]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "invoice_sent";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                // Cv_note::where(["applicant_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "paid"]);
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_invoice_sent';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();

                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){

                    return response()->json(['success' => true, 'message' => 'Client CV moved to invoice sent Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to invoice sent Successfully']);

            }
        }
        elseif (!empty($invoice_revert) && ($invoice_revert == 'revert_invoice')) {
            $audit_data['action'] = "Start Date";
            Client::where("id", $applicant_id)->update(['is_in_crm_start_date' => 1, 'is_crm_interview_attended' => 0, 'is_in_crm_invoice' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "start_date";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Attended To Start Date');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_start_date';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){

                    return response()->json(['success' => true, 'message' => 'CLient CV moved to Start Date Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not client CV moved to Start Date Successfully']);

            }
        }
        elseif (!empty($dispute) && ($dispute == 'dispute')) {
            $audit_data['action'] = "Dispute";
            Client::where("id", $applicant_id)->update(['is_in_crm_dispute' => 1, 'is_in_crm_invoice' => 0,'is_in_crm_invoice_sent' => 0]);
            CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "dispute";
            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Dispute Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Applicant CV moved to Dispute Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Applicant CV moved to Dispute Successfully']);

            }
        } elseif (!empty($final_save) && ($final_save == 'final_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = 'active';
                $history->sub_stage = 'crm_final_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Sale not found!!']);

            }
        }
    }

    public function invoiceActionSent(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Invoice tab */
        $paid = $request->Input('paid');
        // $invoice_sent = $request->Input('invoice_sent');
        $dispute = $request->Input('dispute');
        $final_save = $request->Input('final_save');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';


        if (!empty($paid) && ($paid == 'paid')) {
            $audit_data['action'] = "Paid";
            Client::where("id", $applicant_id)->update(['is_in_crm_paid' => 1, 'is_in_crm_invoice' => 0,'is_in_crm_invoice_sent' =>0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");;
            $crm_notes->moved_tab_to = "paid";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                 CvNote::where(["applicant_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "paid"]);
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_paid';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");;
                $history->save();

//                $dispatcher = Applicant::getEventDispatcher();
//                Applicant::unsetEventDispatcher();
                $update_columns = ['paid_status' => 'close', 'paid_timestamp' => Carbon::now()];
                $update_applicant = Client::where('id', '=', $applicant_id)->update($update_columns);
//                Applicant::setEventDispatcher($dispatcher);

//                if ($update_applicant) {
//                    /*** activity log */
//                    $action_observer = new ActionObserver();
//                    $action_observer->changeCvStatus($applicant_id, $update_columns, 'Closed');
//                }

                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                     $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Paid Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Cient CV moved to Paid Successfully!']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Applicant CV moved to Paid Successfully!!']);

            }
        }elseif (!empty($dispute) && ($dispute == 'dispute')) {
            $audit_data['action'] = "Dispute";
            Client::where("id", $applicant_id)->update(['is_in_crm_dispute' => 1, 'is_in_crm_invoice' => 0,'is_in_crm_invoice_sent' => 0]);
            CvNote::where(["applicant_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            QualityNote::where(["applicant_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");;
            $crm_notes->moved_tab_to = "dispute";
            $crm_notes->status = "active";
            $crm_notes->save();
            CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->stage = 'crm';
                $history->status = "active";
                $history->sub_stage = 'crm_dispute';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");;
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Dispute Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Client CV moved to Dispute Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Client CV moved to Dispute Successfully']);

            }
        } elseif (!empty($final_save) && ($final_save == 'final_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");;
            $crm_notes->moved_tab_to = "final_save";
            $crm_notes->status = "active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Invoice');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
               History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_final_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");;
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                   $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Not Note saved Successfully!']);

            }
        }
    }
    public function disputeAction(Request $request)
    {
//        dd($request->all());
        date_default_timezone_set('Europe/London');

        /*** Invoice tab */
        $dispute_revert_invoice = $request->Input('dispute_revert_invoice');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        $sale = Sale::find($sale_id);
        if ($sale) {
            $sent_cv_count = CvNote::where(['sale_id' => $sale_id, 'status' => 'active'])->count();
            if ($sent_cv_count < $sale->send_cv_limit) {
                if (!empty($dispute_revert_invoice) && ($dispute_revert_invoice == 'dispute_revert_invoice')) {
                    $audit_data['action'] = "Invoice";
                    CvNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "active"]);
                    CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->whereIn('moved_tab_to', ['cv_sent', 'cv_sent_saved', 'cv_sent_request', 'request_save', 'request_confirm', 'prestart_save', 'start_date', 'start_date_save', 'start_date_back', 'interview_attended', 'interview_save'])->update(["status" => "active"]);
                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "invoice";
                    $crm_notes->status = "active";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Invoice');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                        History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
                        $history->user_id = $auth_user;
                        $history->sale_id = $sale_id;
                        $history->stage = 'crm';
                        $history->status = 'active';
                        $history->sub_stage = 'crm_invoice';
                        $history->history_added_date = Carbon::now()->format("Y-m-d");
                        $history->history_added_time = Carbon::now()->format("H:i:s");
                        $history->save();
                        $last_inserted_history = $history->id;
                        if($last_inserted_history > 0){
                            $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV reverted Invoice Successfully
						</div>';
                            return response()->json(['success' => true, 'message' => 'Client CV reverted Invoice Successfully']);

                        } else {
                            return response()->json(['success' => false, 'message' => 'Not Client CV reverted Invoice Successfully!']);

                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Not Client CV reverted Invoice Successfully!']);

                    }
                }
            } else {
                            return response()->json(['success' => false, 'message' => 'You cannot perform this action. Send CV Limit for this Sale has reached maximum!!']);
            }
        } else {

            return response()->json(['success' => false, 'message' => 'Sale not found!!']);

        }
    }

    public function paidAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Paid tab */
        $paid_status = $request->Input('paid_status');
//dd('sa');
        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <span class="font-weight-semibold"> WHOOPS!</span> Something Went Wrong!!
        </div>';
        $update_paid_status = NULL;
        $msg = '';
        if (!empty($paid_status)) {
            if ($paid_status == 'Open') {
                $audit_data['action'] = "Open Applicant CV";
                $update_paid_status = 'open';
                $msg = 'Opened';
            } elseif ($paid_status == 'Close') {
                $audit_data['action'] = "Close Applicant CV";
                $update_paid_status = 'close';
                $msg = 'Closed';
            }
            $update_columns = ['paid_status' => $update_paid_status, 'paid_timestamp' => Carbon::now()];
            $updated = Client::where('id', $applicant_id)->update($update_columns);
            if ($updated) {

                /*** activity log */
//                $action_observer = new ActionObserver();
//                $action_observer->changeCvStatus($applicant_id, $update_columns, $msg);


            }
            return response()->json(['success' => true, 'message' => 'Applicant CV ' . $msg . ' Successfully']);

        } else {
            return response()->json(['success' => false, 'message' => 'something']);

        }
    }

    public function rejectByRequestAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Rejected By Request tab */
        $rejected_request_revert_sent_cvs = $request->input('rejected_request_revert_sent_cvs');
        $rejected_request_revert_request = $request->input('rejected_request_revert_request');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        $sale = Sale::find($sale_id);
        if ($sale) {
            $sent_cv_count = CvNote::where(['sale_id' => $sale_id, 'status' => 'active'])->count();
            if ($sent_cv_count < $sale->send_cv_limit) {
                if (!empty($rejected_request_revert_sent_cvs) && ($rejected_request_revert_sent_cvs == 'rejected_request_revert_sent_cvs')) {
                    CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "active"]);
                    QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id, "moved_tab_to" => "cleared"])->update(["status" => "active"]);
                    CrmNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved", "cv_sent_request"])->update(["status" => "disable"]);
                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "cv_sent";
                    $crm_notes->status="active";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Rejected CV revert to Sent CVs');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                        History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
                        $history->user_id = $auth_user;
                        $history->sale_id = $sale_id;
                        $history->status = 'active';
                        $history->stage = 'crm';
                        $history->sub_stage = 'crm_save';
                        $history->history_added_date = Carbon::now()->format("Y-m-d");
                        $history->history_added_time = Carbon::now()->format("H:i:s");
                        $history->save();
                        $last_inserted_history = $history->id;
                        if($last_inserted_history > 0){

                            return response()->json(['success' => true, 'message' => 'Client CV reverted Sent CVs Successfully']);


                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Not Client CV reverted Sent CVs Successfully']);

                    }
                } elseif (!empty($rejected_request_revert_request) && ($rejected_request_revert_request == 'rejected_request_revert_request')) {
                    CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "active"]);

                    /*** get latest sent cv record */
                    $latest_sent_cv = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "cv_sent")->latest()->first();
                    $all_cv_sent_saved = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->where("moved_tab_to", "cv_sent_saved")
                        ->where('created_at', '>=', $latest_sent_cv->created_at)->get();
                    $crm_notes_ids[0] = $latest_sent_cv->id;
                    foreach ($all_cv_sent_saved as $cv) {
                        $crm_notes_ids[] = $cv->id;
                    }
                    CrmNote::whereIn('id', $crm_notes_ids)->update(["status" => "active"]);
//                        Crm_note::where(["applicant_id" => $applicant_id, "sales_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved"])->update(["status" => "active"]);

                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "cv_sent_request";
                    $crm_notes->status="active";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Request');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                        History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
                        $history->user_id = $auth_user;
                        $history->sale_id = $sale_id;
                        $history->status = 'active';
                        $history->stage = 'crm';
                        $history->sub_stage = 'crm_request';
                        $history->history_added_date = Carbon::now()->format("Y-m-d");
                        $history->history_added_time = Carbon::now()->format("H:i:s");
                        $history->save();
                        $last_inserted_history = $history->id;
                        if($last_inserted_history > 0){
                             $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span>
						</div>';
                            return response()->json(['success' => true, 'message' => 'Client CV reverted Request Successfully']);


                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Client CV reverted Request Successfully']);

                    }
                }
            } else {
//                echo
//                '<div class="alert alert-danger border-0 alert-dismissible">
//                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
//                        <span class="font-weight-semibold"> WHOOPS!</span> You cannot perform this action. Send CV Limit for this Sale has reached maximum!!
//                    </div>';
                return response()->json(['success' => false, 'message' => 'SORRY! You cannot perform this action. Send CV Limit for this Sale has reached maximum!!']);

            }
        } else {
            return response()->json(['success' => false, 'message' => 'Sale not found!!']);


        }
    }

    public function revertSentCvAction(Request $request)
    {

        date_default_timezone_set('Europe/London');

        /*** Rejected CV tab */
        $rejected_cv_revert_sent_cvs_value = $request->Input('rejected_cv_revert_sent_cvs');

        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        $sale = Sale::find($sale_id);
        if ($sale) {
            $sent_cv_count = CvNote::where(['sale_id' => $sale_id, 'status' => 'active'])->count();
            if ($sent_cv_count < $sale->send_cv_limit) {
                if (!empty($rejected_cv_revert_sent_cvs_value) && ($rejected_cv_revert_sent_cvs_value == 'rejected_cv_revert_sent_cvs')) {
                    $crm_note_id = CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id, 'moved_tab_to' => 'cv_sent_reject'])->select('id')->latest()->first()->id;
                    Crm_rejected_cv::where(["client_id" => $applicant_id, "sale_id" => $sale_id, 'crm_note_id' => $crm_note_id])->update(["status" => "disable"]);
                    CvNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "active"]);
                    QualityNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->update(["status" => "active"]);
                    CrmNote::where(["client_id" => $applicant_id, "sale_id" => $sale_id])->whereIn("moved_tab_to", ["cv_sent", "cv_sent_saved"])->update(["status" => "disable"]);
                    $crm_notes = new CrmNote();
                    $crm_notes->client_id = $applicant_id;
                    $crm_notes->user_id = $auth_user;
                    $crm_notes->sale_id = $sale_id;
                    $crm_notes->details = $details;
                    $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
                    $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
                    $crm_notes->moved_tab_to = "cv_sent";
                    $crm_notes->save();

                    /*** activity log
                     * $this->action_observer->action($audit_data, 'CRM > Rejected CV revert to Sent CVs');
                     */

                    $last_inserted_note = $crm_notes->id;
                    if ($last_inserted_note > 0) {
                         History::where([
                            "client_id" => $applicant_id,
                            "sale_id" => $sale_id
                        ])->update(["status" => "disable"]);
                        $history = new History();
                        $history->client_id = $applicant_id;
                        $history->user_id = $auth_user;
                        $history->sale_id = $sale_id;
                        $history->status = 'active';
                        $history->stage = 'crm';
                        $history->sub_stage = 'crm_save';
                        $history->history_added_date = Carbon::now()->format("Y-m-d");
                        $history->history_added_time = Carbon::now()->format("H:i:s");
                        $history->save();
                        $last_inserted_history = $history->id;
                        if($last_inserted_history > 0){
                            $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV reverted Sent CVs Successfully
						</div>';
                            return response()->json(['success' => true, 'message' => 'Client CV reverted Sent CVs Successfully']);


                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Sale not found!!']);


                    }
                }
            } else {

                return response()->json(['success' => false, 'message' => 'SORRY! You cannot perform this action. Send CV Limit for this Sale has reached out!!']);

            }
        } else {
            return response()->json(['success' => false, 'message' => 'SORRY! You cannot perform this action. Send CV Limit for this Sale has reached out!!']);

        }
    }

    public function sentCvAction(Request $request)
    {
        date_default_timezone_set('Europe/London');

        /*** Sent CVs tab */
        $cv_sent_reject_value = $request->Input('cv_sent_reject');
        $cv_sent_request_value = $request->Input('cv_sent_request');
        $cv_sent_save_value = $request->Input('cv_sent_save');
        $audit_data['action'] = '';
        $audit_data['applicant'] = $applicant_id = $request->Input('app_hidden_id');
        $auth_user = Auth::user()->id;
        $audit_data['sale'] = $sale_id = $request->Input('job_hidden_id');
        $audit_data['details'] = $details = $request->Input('details');
        $unit_name = Sale::join('units', 'sales.head_office_unit', '=', 'units.id')
            ->where('sales.id','=', $sale_id)
            ->select('units.unit_name')->first();
        $unit_name =  $unit_name->unit_name;
        $app_res = Client::select('app_phone','app_name')->find($applicant_id);
        $applicant_phone = $app_res->app_phone;
        $applicant_name = $app_res->app_name;

        $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> WHOOPS! Something Went Wrong!!
                </div>';

        if (!empty($cv_sent_save_value) && ($cv_sent_save_value == 'cv_sent_save')) {
            $audit_data['action'] = "Save";
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_saved";
            $crm_notes->status="active";
            $crm_notes->save();

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                 History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_save';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                   $html = '<div class="alert alert-success border-0 alert-dismissible">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
							<span class="font-weight-semibold">'.$request->input('module').'</span> Note saved Successfully
						</div>';
                    return response()->json(['success' => true, 'message' => 'Note saved Successfully']);

                }

            } else {
                return response()->json(['success' => false, 'message' => 'Note not saved Successfully']);

            }
        } elseif (!empty($cv_sent_request_value) && ($cv_sent_request_value == 'cv_sent_request')) {

            $audit_data['action'] = "Request";
            Client::where("id", $applicant_id)->update(['is_in_crm_request' => 1, 'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
            $crm_notes->user_id = $auth_user;
            $crm_notes->sale_id = $sale_id;
            $crm_notes->details = $details;
            $audit_data['added_date'] = $crm_notes->crm_added_date = Carbon::now()->format("Y-m-d");
            $audit_data['added_time'] = $crm_notes->crm_added_time = Carbon::now()->format("H:i:s");
            $crm_notes->moved_tab_to = "cv_sent_request";
            $crm_notes->status="active";
            $crm_notes->save();
            QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id, "moved_tab_to" => "cleared"])->update(["status" => "disable"]);

            /*** activity log
            $this->action_observer->action($audit_data, 'CRM > Sent CVs');
             */

            $last_inserted_note = $crm_notes->id;
            if ($last_inserted_note > 0) {
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
                $history->user_id = $auth_user;
                $history->sale_id = $sale_id;
                $history->status = 'active';
                $history->stage = 'crm';
                $history->sub_stage = 'crm_request';
                $history->history_added_date = Carbon::now()->format("Y-m-d");
                $history->history_added_time = Carbon::now()->format("H:i:s");
                $history->save();
                $last_inserted_history = $history->id;
                if($last_inserted_history > 0){
                    // $applicant_numbers='07597019065';

//                    $html = '<div class="alert alert-success border-0 alert-dismissible">
//							<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
//							<span class="font-weight-semibold">'.$request->input('module').'</span> Applicant CV moved to Request successfully
//						</div>';
//                    echo $html;
                    return response()->json(['success' => true, 'message' => 'Client  CV moved to Request successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Sorry! Data is invalid']);

            }
        } elseif (!empty($cv_sent_reject_value) && ($cv_sent_reject_value == 'cv_sent_reject')) {
            $audit_data['action'] = "Reject";
            $audit_data['reject_reason'] = $reject_reason = $request->Input('reject_reason');
            Client::where("id", $applicant_id)->update(['is_in_crm_reject' => 1,
                'is_interview_confirm' => 0]);
            $crm_notes = new CrmNote();
            $crm_notes->client_id = $applicant_id;
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
                $crm_rejected_cv = new Crm_rejected_cv();
                $crm_rejected_cv->client_id = $applicant_id;
                $crm_rejected_cv->sale_id = $sale_id;
                $crm_rejected_cv->user_id = $auth_user;
                $crm_rejected_cv->crm_note_id = $last_inserted_note;
                $crm_rejected_cv->reason = $reject_reason;
                $crm_rejected_cv->crm_rejected_cv_note = $details;
                $crm_rejected_cv->crm_rejected_cv_date = Carbon::now()->format("Y-m-d");
                $crm_rejected_cv->crm_rejected_cv_time = Carbon::now()->format("H:i:s");
                $crm_rejected_cv->save();
                $last_crm_reject_id = $crm_rejected_cv->id;
                CvNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                QualityNote::where(["client_id" => $applicant_id,"sale_id" => $sale_id])->update(["status" => "disable"]);
                History::where([
                    "client_id" => $applicant_id,
                    "sale_id" => $sale_id
                ])->update(["status" => "disable"]);
                $history = new History();
                $history->client_id = $applicant_id;
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

                    return response()->json(['success' => true, 'message' => 'Client  CV moved to rejected successfully']);

                }
            } else {
                return response()->json(['success' => false, 'message' => 'Reject cv error']);

            }
        }
    }


}
