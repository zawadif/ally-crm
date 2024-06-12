<?php

namespace App\Http\Controllers;


use App\Models\Audit;
use App\Models\Client;
use App\Models\CrmNote;
use App\Models\CvNote;
use App\Models\History;
use App\Models\Office;
use App\Models\Sale;
use App\Models\Specialist_job_titles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Enums\NotificationTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use function Symfony\Component\VarDumper\Dumper\esc;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        date_default_timezone_set('Europe/London');

             $userId=Auth::id();

            $users = User::
            where('status', UserStatusEnum::ACTIVE);
            $sales=Sale::whereIn('status',['active','disable'])->count();
            $clients=Client::whereIn('app_status',['active','disable'])->count();

            $userCount = $users->count();

         $saleOpen=Sale::where('status','active')->whereDate('created_at',Carbon::today())->count();
        $pendingSales=Sale::where('status','pending')->count();
        $pendingSalesToday=Sale::where('status','pending')->whereDate('created_at',Carbon::today())->count();
        $closeSalesToday=Sale::where('status','disable')->whereDate('created_at',Carbon::today())->count();
        $holdSalesToday=Sale::where('is_on_hold','1')->whereDate('created_at',Carbon::today())->count();
//$today=Carbon::now();
//dd($today);
        $clientQualified=Client::where('app_status','active')->where('app_job_category','nurses')->whereDate('updated_at',Carbon::today())->count();
        $clientNonQualified=Client::where('app_status','active')->where('app_job_category','non-nurses')->whereDate('updated_at',Carbon::today())->count();
        $clientCallback=Client::where('app_status','active')->where('temp_not_interested','1')->whereDate('updated_at',Carbon::today())->count();
        $clientBlock=Client::where('app_status','active')->where('is_blocked',1)->whereDate('updated_at',Carbon::today())->count();
        $today=Carbon::today();
        $quality['daily_cvs'] = CvNote::where('send_added_date', $today->format('Y-m-d'))->count();
        $quality['daily_cvs_rejected'] = History::where(['sub_stage' => 'quality_reject', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();
//        dd($quality['daily_cvs_rejected']);
        $quality['daily_cvs_cleared'] = History::where(['sub_stage' => 'quality_cleared'])->where('history_added_date', $today->format('Y-m-d'))->count();

        $crm_data["Crm Stage"] = "Number of Applicants";
//        $crm_data['crm_sent'] = Crm_note::where('moved_tab_to', '=', 'cv_sent')->whereIn('crm_notes.id', function($query){ $query->select(DB::raw('MAX(id) FROM crm_notes as c WHERE c.moved_tab_to = "cv_sent" AND c.applicant_id=crm_notes.applicant_id AND c.sales_id=crm_notes.sales_id')); })->where('crm_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_sent'] = $quality['daily_cvs_cleared'];
        $crm_data['crm_rejected'] = History::where(['sub_stage' => 'crm_reject', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();
        /***
        $crm_data['crm_requested'] = History::where('history.sub_stage', 'crm_request')
        ->whereIn('history.id', function ($query) {
        $query->select(DB::raw('MAX(h.id) FROM history as h WHERE h.sub_stage="crm_request" AND history.applicant_id=h.applicant_id AND history.sale_id=h.sale_id'));
        })->where('history_added_date', $today->format('Y-m-d'))->count();
         */
        $crm_data['crm_requested'] = History::where('histories.sub_stage', 'crm_request')
            ->whereIn('histories.id', function ($query) use ($today) {
                $query->select(DB::raw('MAX(h.id) FROM histories AS h
                WHERE h.sub_stage = "crm_request"
                AND histories.client_id = h.client_id
                AND histories.sale_id = h.sale_id
                AND histories.id > (
                    SELECT MAX(hh.id) FROM histories AS hh
                    WHERE ( hh.sub_stage = "quality_cleared" OR hh.sub_stage = "crm_save" )
                    AND histories.client_id = hh.client_id AND histories.sale_id = hh.sale_id
                )'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_request_rejected'] = History::where(['sub_stage' => 'crm_request_reject', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();

        // decline daily stat
        // rebook daily stat

        /***
        $crm_data['crm_confirmed'] = History::where('history.sub_stage', "crm_request_confirm")
        ->whereIn('history.id', function ($query) {
        $query->select(DB::raw('MAX(h.id) FROM history as h WHERE h.sub_stage="crm_request_confirm" AND history.applicant_id=h.applicant_id AND history.sale_id=h.sale_id'));
        })->where('history.history_added_date', $today->format('Y-m-d'))->count();
         */
        $crm_data['crm_confirmed'] = History::whereIn('histories.sub_stage', ['crm_request_confirm','revert_to_crm_request'])
            ->whereIn('histories.id', function ($query) use ($today) {
                $query->select(DB::raw('MAX(h.id) FROM histories AS h
                WHERE h.sub_stage IN ("crm_request_confirm","revert_to_crm_request")
	            AND histories.client_id = h.client_id
	            AND histories.sale_id = h.sale_id
	            AND histories.id > (
		            SELECT MAX(hh.id) FROM histories AS hh
		            WHERE hh.sub_stage = "crm_request"
		            AND histories.client_id = hh.client_id
		            AND histories.sale_id = hh.sale_id
            	)'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();

        $crm_data['crm_prestart_attended'] = History::where('histories.sub_stage', "crm_interview_attended")
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage="crm_interview_attended" AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_rebook'] = History::where('histories.sub_stage', "crm_rebook")
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage="crm_rebook" AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();

        $crm_data['crm_not_attended'] = History::where(['sub_stage' => 'crm_interview_not_attended', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();
//        $crm_data['crm_date_started'] = History::whereIn('history.sub_stage', ['crm_start_date'])->where('history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_declined'] = History::where(['sub_stage' => 'crm_declined', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();

        $crm_data['crm_date_started'] = History::whereIn('histories.sub_stage', ['crm_start_date', 'crm_start_date_back'])
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE ( h.sub_stage="crm_start_date" OR h.sub_stage="crm_start_date_back" ) AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();
//        dd($crm_data['crm_date_started']);

        $crm_data['crm_start_date_held'] = History::where(['sub_stage' => 'crm_start_date_hold', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_invoiced'] = History::whereIn('histories.sub_stage', ['crm_invoice','crm_invoice_sent'])
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage IN ("crm_invoice","crm_invoice_sent") AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->where('histories.history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_disputed'] = History::where(['sub_stage' => 'crm_dispute', 'status' => 'active'])->where('history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_paid'] = History::where('sub_stage', 'crm_paid')->where('history_added_date', $today->format('Y-m-d'))->count();
        $crm_total['daily'] = $crm_data['crm_sent'] + $crm_data['crm_rejected'] + $crm_data['crm_requested'] + $crm_data['crm_request_rejected'] + $crm_data['crm_confirmed'] + $crm_data['crm_prestart_attended'] + $crm_data['crm_not_attended'] + $crm_data['crm_date_started'] + $crm_data['crm_start_date_held'] + $crm_data['crm_invoiced'] + $crm_data['crm_disputed'] + $crm_data['crm_paid'];
        /*** /daily stats */



        return view('home', ['userCount' => $userCount, 'clients' => $clients, 'sales' => $sales,
                'pendingSales' => $pendingSales,'saleOpen'=>$saleOpen,
                'pendingSalesToday'=>$pendingSalesToday,'holdSalesToday'=>$holdSalesToday,'closeSalesToday'=>$closeSalesToday
            ,'clientQualified'=>$clientQualified,'clientNonQualified'=>$clientNonQualified,
            'quality'=>$quality,'clientCallback'=>$clientCallback,'clientBlock'=>$clientBlock,'crm_data'=>$crm_data]);
//        }
    }
    public function userDetailDashbaord(){
        if(\request()->ajax()){
            $data =  User::with('roles')->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['Super_admin']);
            })->get();;
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                    return '<button type="submit" class="btn btn-sm btn-info user-statistics" data-user_key="'.$user->id.'" data-user_name="'.$user->fullName.'">Detail</button>';
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
    }

    public function interceptUrl()
    {
        // if (App::environment(['local']) ||  App::environment(['staging'])) {
        //     Auth::logout();
        // }
        return redirect('/interceptUrl?status=true');
    }

    public function webportal()
    {
        return view('webportal.webportDashboard');
    }

    public function getUsers()
    {
        return view('datatable');
    }

    public function userStatistics(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_key' => 'required|exists:users,id',
//                'start_date' => 'required|date_format:d-m-Y',
//                'end_date' => 'required|date_format:d-m-Y'
            ]
        );

        if ($validator->passes()) {

            $start_date = Carbon::parse($request->input('start_date'));
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
//            dd($start_date,$end_date);
            $user_id = $request->input('user_key');
            $user = User::find($user_id);
//            dd($user->email);
            $roles = implode($user->roles->pluck('name', 'name')->all());
            // echo json_encode($roles);exit();
            $user_role = empty($roles) ? '' : $roles;

            $user_stats['no_of_send_cvs_from_cv_notes'] = 0;
            $user_stats['send_cvs_from_cv_notes'] = [];
            $prev_user_stats['crm_start_date']=0;
            $prev_user_stats['crm_invoice']=0;
            $prev_user_stats['crm_paid']=0;
            $user_stats_updated='';

            if (in_array($user_role, ['Sales', 'Sale and CRM'])) {

                $sales = Sale::where('user_id', $user_id)->whereIn('status', ['active','disable'])->whereBetween('created_at', [$start_date, $end_date])->get();
                $user_stats['close_sales'] = Audit::join('sales', 'sales.id', '=', 'audits.auditable_id')
                    ->where(['audits.message' => 'sale-closed', 'audits.auditable_type' => 'Horsefly\\Sale'])
                    ->where('sales.user_id', '=', $user_id)
                    ->whereBetween('sales.created_at', [$start_date, $end_date])
                    ->whereBetween('audits.created_at', [$start_date, $end_date])->count();
//                $user_stats['close_sales'] = Sale::where(['status' => 'disable', 'user_id' => $user_id])->whereBetween('created_at', [$start_date, $end_date])->count();
                $user_stats['open_sales'] = $sales->count() - $user_stats['close_sales'];
                $user_stats['psl_offices'] = Office::where(['status' => 'active', 'type' => 'psl', 'user_id' => $user_id])->whereBetween('created_at', [$start_date, $end_date])->count();
                $user_stats['non_psl_offices'] = Office::where(['status' => 'active', 'type' => 'non psl', 'user_id' => $user_id])->whereBetween('created_at', [$start_date, $end_date])->count();

                foreach ($sales as $sale) {

                    $send_cvs_from_cv_notes = CvNote::where('sale_id', '=', $sale->id)
                        ->whereBetween('created_at', [$start_date, $end_date])->select('client_id', 'sale_id')->get();
                    $user_stats_updated = CvNote::where('user_id', '=', $user_id)
                        ->select('client_id', 'sale_id')
                        ->where('created_at','<', $start_date)->whereBetween('updated_at', [$start_date, $end_date])->get();
                    foreach ($send_cvs_from_cv_notes as $send_cvs_from_cv_note) {
                        $user_stats['send_cvs_from_cv_notes'][] = $send_cvs_from_cv_note;


                        /*** Quality  CVs*/
                        $user_stats['no_of_send_cvs_from_cv_notes']++;
                    }
                }
            } else {
                $user_stats['send_cvs_from_cv_notes'] = CvNote::where('user_id', '=', $user_id)->whereBetween('created_at', [$start_date, $end_date])->select('client_id', 'sale_id')->get();

                $user_stats_updated = CvNote::where('user_id', '=', $user_id)
                    ->select('client_id', 'sale_id')
                    ->where('created_at','<', $start_date)->whereBetween('updated_at', [$start_date, $end_date])->get();
                $user_stats['no_of_send_cvs_from_cv_notes'] = $user_stats['send_cvs_from_cv_notes']->count();
            }

            $user_stats['cvs_rejected'] = $user_stats['cvs_cleared'] = $user_stats['crm_sent_cvs'] = $user_stats['crm_rejected_cv'] = $user_stats['crm_request'] = $user_stats['crm_rejected_by_request'] = $user_stats['crm_confirmation'] = $user_stats['crm_rebook'] = $user_stats['crm_attended'] = $user_stats['crm_not_attended'] = $user_stats['crm_start_date'] = $user_stats['crm_start_date_hold'] = $user_stats['crm_declined'] = $user_stats['crm_invoice'] = $user_stats['crm_dispute'] = $user_stats['crm_paid'] = 0;
            foreach ($user_stats['send_cvs_from_cv_notes'] as $key => $cv) {

                $cv_cleared = History::where(['sub_stage' => 'quality_cleared', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                    ->whereBetween('updated_at', [$start_date, $end_date])->first();
                if ($cv_cleared) {
                    $user_stats['cvs_cleared']++;
                    /*** Sent CVs */
                    $user_stats['crm_sent_cvs']++;


                    /*** Rejected CV */
                    $crm_rejected_cv = History::where(['sub_stage' => 'crm_reject', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])->whereBetween('created_at', [$start_date, $end_date])->first();
                    if ($crm_rejected_cv) {
                        $user_stats['crm_rejected_cv']++;
                        continue;
                    }


                    /*** Request */
                    $crm_request = History::where(['sub_stage' => 'crm_request', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                        ->whereIn('id', function ($query) {
                            $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_request" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));
                        })->whereBetween('created_at', [$start_date, $end_date])->first();

                    $crm_sent_cv = CrmNote::where(['crm_notes.moved_tab_to' => 'cv_sent', 'crm_notes.client_id' => $cv->client_id, 'crm_notes.sale_id' => $cv->sale_id])
                        ->whereIn('crm_notes.id', function ($query) {
                            $query->select(DB::raw('MAX(id) FROM crm_notes as c WHERE c.moved_tab_to="cv_sent" and c.sale_id=crm_notes.sale_id and c.client_id=crm_notes.client_id'));
                        })->whereBetween('crm_notes.created_at', [$start_date, $end_date])->first();

                    if ($crm_request && $crm_sent_cv && (Carbon::parse($crm_request->history_added_date . ' ' . $crm_request->history_added_time)->gt($crm_sent_cv->created_at))) {
                        $user_stats['crm_request']++;


                        /*** Rejected By Request */
                        $crm_rejected_by_request = History::where(['sub_stage' => 'crm_request_reject', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                            ->whereBetween('created_at', [$start_date, $end_date])->first();
                        if ($crm_rejected_by_request) {
                            $user_stats['crm_rejected_by_request']++;
                            continue;
                        }


                        /*** Confirmation */
                        $crm_confirmation = History::where(['sub_stage' => 'crm_request_confirm', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                            ->whereIn('id', function ($query) {
                                $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_request_confirm" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));
                            })->whereBetween('created_at', [$start_date, $end_date])->first();
                        if ($crm_confirmation && (Carbon::parse($crm_confirmation->history_added_date . ' ' . $crm_confirmation->history_added_time)->gt(Carbon::parse($crm_request->history_added_date . ' ' . $crm_request->history_added_time)))) {
                            $user_stats['crm_confirmation']++;

                            /*** Rebook */
                            $crm_rebook = History::where(['sub_stage' => 'crm_reebok', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                                ->whereBetween('created_at', [$start_date, $end_date])->first();
                            if ($crm_rebook) {
                                $user_stats['crm_rebook']++;
                                continue;
                            }






                            /*** Attended Pre-Start Date */
                            $crm_attended = History::where(['sub_stage' => 'crm_interview_attended', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                                ->whereIn('id', function ($query) {
                                    $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_interview_attended" and h.sale_id=histories.sale_id and h.client_id=history.client_id'));
                                })->whereBetween('created_at', [$start_date, $end_date])->first();
                            if ($crm_attended) {
                                $user_stats['crm_attended']++;

                                /*** Declined */
                                $crm_declined = History::where(['sub_stage' => 'crm_declined', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                                    ->whereBetween('created_at', [$start_date, $end_date])->first();
                                if ($crm_declined) {
                                    $user_stats['crm_declined']++;
                                    continue;
                                }

                                /*** Not Attended */
                                $crm_not_attended = History::where(['sub_stage' => 'crm_interview_not_attended', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                                    ->whereBetween('created_at', [$start_date, $end_date])->first();
                                if ($crm_not_attended) {
                                    $user_stats['crm_not_attended']++;
                                    continue;
                                }

                                /*** Start Date */
                                $crm_start_date = History::where(['history.sub_stage' => 'crm_start_date', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                                    ->whereBetween('created_at', [$start_date, $end_date])->first();

                                $crm_start_date_back = History::where(['history.sub_stage' => 'crm_start_date_back', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                                    ->whereIn('id', function ($query) {
                                        $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_start_date_back" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));
                                    })->whereBetween('created_at', [$start_date, $end_date])->first();
                                if (($crm_start_date && !$crm_start_date_back) || ($crm_start_date && $crm_start_date_back)) {

                                    $user_stats['crm_start_date']++;
                                    $crm_start_date = $crm_start_date_back ? $crm_start_date_back : $crm_start_date;


                                    /*** Start Date Hold */
                                    $crm_start_date_hold = History::where(['history.sub_stage' => 'crm_start_date_hold', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                                        ->whereBetween('created_at', [$start_date, $end_date])->first();
                                    if ($crm_start_date_hold) {
                                        $user_stats['crm_start_date_hold']++;
                                        continue;
                                    }



                                    /*** Invoice */
                                    $crm_invoice = History::where(['histories.sub_stage' => 'crm_invoice', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                                        ->whereIn('id', function ($query) {
                                            $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_invoice" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));
                                        })->whereBetween('created_at', [$start_date, $end_date])->first();

                                    if ($crm_invoice) {
                                        $user_stats['crm_invoice']++;


                                        /*** Dispute */
                                        $crm_dispute = History::where(['sub_stage' => 'crm_dispute', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                                            ->whereBetween('created_at', [$start_date, $end_date])->first();
                                        if ($crm_dispute) {
                                            $user_stats['crm_dispute']++;
                                            continue;
                                        }


                                        /*** Paid */
                                        $crm_paid = History::where(['sub_stage' => 'crm_paid', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                                            ->whereBetween('created_at', [$start_date, $end_date])->first();
                                        if ($crm_paid) {
                                            $user_stats['crm_paid']++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else {
                    $cv_rejected = History::where(['sub_stage' => 'quality_reject', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id, 'status' => 'active'])
                        ->whereBetween('created_at', [$start_date, $end_date])->first();
                    if ($cv_rejected) {
                        $user_stats['cvs_rejected']++;
                    }
                }
            }


            // ---------------------------------------------------Last month stats -------------------------------------------------------------

            if($user_stats_updated){
                foreach ($user_stats_updated as $key => $cv)
                {
                    /*** Start Date */
                    $crm_start_date = History::where(['histories.sub_stage' => 'crm_start_date', 'client_id' => $cv->client_id, 'sale_id' =>
                        $cv->sale_id])->whereBetween('created_at', [$start_date, $end_date])->first();
                    $crm_start_date_back = History::where(['histories.sub_stage' => 'crm_start_date_back', 'client_id' => $cv->client_id, 'sale_id'=> $cv->sale_id])->whereIn('id', function ($query) {
                        $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_start_date_back" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));})->whereBetween('created_at', [$start_date, $end_date])->first();
                    if (($crm_start_date && !$crm_start_date_back) || ($crm_start_date && $crm_start_date_back))
                    {
                        $prev_user_stats['crm_start_date']++;
                        $crm_start_date = $crm_start_date_back ? $crm_start_date_back : $crm_start_date;
                    }
                    /*** Invoice */
                    $crm_invoice = History::where(['histories.sub_stage' => 'crm_invoice', 'client_id' => $cv->client_id,
                        'sale_id' => $cv->sale_id])
                        ->whereIn('id', function ($query) {
                            $query->select(DB::raw('MAX(id) FROM histories h WHERE h.sub_stage="crm_invoice" and h.sale_id=histories.sale_id and h.client_id=histories.client_id'));})
                        ->whereBetween('created_at', [$start_date, $end_date])->first();

                    if ($crm_invoice) {
                        $prev_user_stats['crm_invoice']++;
                    }



                    /*** Paid */
                    $crm_paid = History::where(['sub_stage' => 'crm_paid', 'client_id' => $cv->client_id, 'sale_id' => $cv->sale_id])
                        ->whereBetween('created_at', [$start_date, $end_date])->first();
                    if ($crm_paid) {
                        $prev_user_stats['crm_paid']++;
                    }

                    //}

                }
            }
            unset($user_stats['send_cvs_from_cv_notes']);
            unset($user_stats['all_send_cvs_from_cv_notes']);
            $user_statistics_modal_body = view('administrator.partial.user_statistics', compact('user_stats', 'prev_user_stats', 'user_role'))->render();
            return $user_statistics_modal_body;
        }
        return response()->json(['error' => $validator->errors()->all()]);
    }



    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        return Datatables::of(User::select('id', 'fullName', 'email', 'createdAt', 'updatedAt')->get())->make(true);
    }



    public function fetchData(Request $request) {
        // Retrieve start and end dates from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // If start date is null, default to today's date
        if (!$startDate) {
            $startDate = Carbon::now();
        }

        // If end date is null, default to today's date
        if (!$endDate) {
            $endDate = Carbon::now()->endOfDay();
        }

        // Fetch data from your database based on the selected date range
        $saleOpen = Sale::where('status', 'active')->whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingSales = Sale::where('status', 'pending')->count();
        $pendingSalesToday = Sale::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count();
        $closeSalesToday = Sale::where('status', 'disable')->whereBetween('created_at', [$startDate, $endDate])->count();
        $holdSalesToday = Sale::where('is_on_hold', '1')->whereBetween('created_at', [$startDate, $endDate])->count();

        $clientQualified = Client::where('app_status', 'active')->where('app_job_category', 'nurses')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $clientNonQualified = Client::where('app_status', 'active')->where('app_job_category', 'non-nurses')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $clientCallback = Client::where('app_status', 'active')->where('temp_not_interested', '1')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $clientBlock = Client::where('app_status', 'active')->where('is_blocked', 1)->whereBetween('updated_at', [$startDate, $endDate])->count();

        $today = Carbon::today();
        $quality['daily_cvs'] = CvNote::whereBetween('created_at', [$startDate, $endDate])->count();
        $quality['daily_cvs_rejected'] = History::where(['sub_stage' => 'quality_reject', 'status' => 'active'])->whereBetween('history_added_date', [$startDate, $endDate])->count();
        $quality['daily_cvs_cleared'] = History::where(['sub_stage' => 'quality_cleared'])->whereBetween('history_added_date', [$startDate, $endDate])->count();

        // Prepare data to be sent as JSON response
        $data = [
            'saleOpen' => $saleOpen,
            'pendingSales' => $pendingSales,
            'pendingSalesToday' => $pendingSalesToday,
            'closeSalesToday' => $closeSalesToday,
            'holdSalesToday' => $holdSalesToday,
            'clientQualified' => $clientQualified,
            'clientNonQualified' => $clientNonQualified,
            'clientCallback' => $clientCallback,
            'clientBlock' => $clientBlock,
            'quality' => $quality
        ];

        // Return data as JSON response
        return response()->json(['data' => $data]);
    }

    public function applicantHomeDetailStats(Request $request){
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userKey = $request->input('user_key');
        $userHome = $request->input('user_home');

        // Perform the query based on the parameters
        $query = DB::table('clients')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('app_job_category', $userHome);

// Get the count of records for each source
        $counts = $query
            ->select('app_source', DB::raw('COUNT(*) as count'))
            ->groupBy('app_source')
            ->pluck('count', 'app_source')
            ->toArray();

// Ensure that all possible source names are included with zero counts
        $applicantSources = ['Total Jobs', 'Reed', 'Niche', 'CV Library', 'Social Media', 'Referral', 'Other Source'];
        foreach ($applicantSources as $source) {
            if (!isset($counts[$source])) {
                $counts[$source] = 0;
            }
        }

// Now $counts array will contain counts for each source name
        return response()->json(['counts' => $counts,'category'=>$userHome]);

    }
    public function fetchCVSData(Request  $request){
        // Get the start and end dates from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // If start or end date is not provided, use the current date
        if (empty($startDate) || empty($endDate)) {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        }
        $quality['daily_cvs_cleared'] = History::where(['sub_stage' => 'quality_cleared'])->whereBetween('histories.history_added_date', [$startDate, $endDate])
        ->count();

        $crm_data["Crm Stage"] = "Number of Applicants";
//        $crm_data['crm_sent'] = Crm_note::where('moved_tab_to', '=', 'cv_sent')->whereIn('crm_notes.id', function($query){ $query->select(DB::raw('MAX(id) FROM crm_notes as c WHERE c.moved_tab_to = "cv_sent" AND c.applicant_id=crm_notes.applicant_id AND c.sales_id=crm_notes.sales_id')); })->where('crm_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_sent'] = $quality['daily_cvs_cleared'];
        $crm_data['crm_rejected'] = History::where(['sub_stage' => 'crm_reject', 'status' => 'active'])
            ->whereBetween('histories.history_added_date', [$startDate, $endDate])
        ->count();
        /***
        $crm_data['crm_requested'] = History::where('history.sub_stage', 'crm_request')
        ->whereIn('history.id', function ($query) {
        $query->select(DB::raw('MAX(h.id) FROM history as h WHERE h.sub_stage="crm_request" AND history.applicant_id=h.applicant_id AND history.sale_id=h.sale_id'));
        })->where('history_added_date', $today->format('Y-m-d'))->count();
         */
        $crm_data['crm_requested'] = History::where('histories.sub_stage', 'crm_request')
            ->whereIn('histories.id', function ($query) use ($startDate,$endDate) {
                $query->select(DB::raw('MAX(h.id) FROM histories AS h
                WHERE h.sub_stage = "crm_request"
                AND histories.client_id = h.client_id
                AND histories.sale_id = h.sale_id
                AND histories.id > (
                    SELECT MAX(hh.id) FROM histories AS hh
                    WHERE ( hh.sub_stage = "quality_cleared" OR hh.sub_stage = "crm_save" )
                    AND histories.client_id = hh.client_id AND histories.sale_id = hh.sale_id
                )'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_data['crm_request_rejected'] = History::where(['sub_stage' => 'crm_request_reject', 'status' => 'active'])
            ->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
//        dd($crm_data['crm_requested']);

        // decline daily stat
        // rebook daily stat

        /***
        $crm_data['crm_confirmed'] = History::where('history.sub_stage', "crm_request_confirm")
        ->whereIn('history.id', function ($query) {
        $query->select(DB::raw('MAX(h.id) FROM history as h WHERE h.sub_stage="crm_request_confirm" AND history.applicant_id=h.applicant_id AND history.sale_id=h.sale_id'));
        })->where('history.history_added_date', $today->format('Y-m-d'))->count();
         */
        $crm_data['crm_confirmed'] = History::whereIn('histories.sub_stage', ['crm_request_confirm','revert_to_crm_request'])
            ->whereIn('histories.id', function ($query) use ($startDate,$endDate) {
                $query->select(DB::raw('MAX(h.id) FROM histories AS h
                WHERE h.sub_stage IN ("crm_request_confirm","revert_to_crm_request")
	            AND histories.client_id = h.client_id
	            AND histories.sale_id = h.sale_id
	            AND histories.id > (
		            SELECT MAX(hh.id) FROM histories AS hh
		            WHERE hh.sub_stage = "crm_request"
		            AND histories.client_id = hh.client_id
		            AND histories.sale_id = hh.sale_id
            	)'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();

        $crm_data['crm_prestart_attended'] = History::where('histories.sub_stage', "crm_interview_attended")
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage="crm_interview_attended" AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_data['crm_rebook'] = History::where('histories.sub_stage', "crm_rebook")
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage="crm_rebook" AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();

        $crm_data['crm_not_attended'] = History::where(['sub_stage' => 'crm_interview_not_attended', 'status' => 'active'])->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
//        $crm_data['crm_date_started'] = History::whereIn('history.sub_stage', ['crm_start_date'])->where('history_added_date', $today->format('Y-m-d'))->count();
        $crm_data['crm_declined'] = History::where(['sub_stage' => 'crm_declined', 'status' => 'active'])->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();

        $crm_data['crm_date_started'] = History::whereIn('histories.sub_stage', ['crm_start_date', 'crm_start_date_back'])
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE ( h.sub_stage="crm_start_date" OR h.sub_stage="crm_start_date_back" ) AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
//        dd($crm_data['crm_date_started']);

        $crm_data['crm_start_date_held'] = History::where(['sub_stage' => 'crm_start_date_hold', 'status' => 'active'])->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_data['crm_invoiced'] = History::whereIn('histories.sub_stage', ['crm_invoice','crm_invoice_sent'])
            ->whereIn('histories.id', function ($query) {
                $query->select(DB::raw('MAX(h.id) FROM histories as h WHERE h.sub_stage IN ("crm_invoice","crm_invoice_sent") AND histories.client_id=h.client_id AND histories.sale_id=h.sale_id'));
            })->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_data['crm_disputed'] = History::where(['sub_stage' => 'crm_dispute', 'status' => 'active'])->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_data['crm_paid'] = History::where('sub_stage', 'crm_paid')->whereBetween('histories.history_added_date', [$startDate, $endDate])->count();
        $crm_total['daily'] = $crm_data['crm_sent'] + $crm_data['crm_rejected'] + $crm_data['crm_requested'] + $crm_data['crm_request_rejected'] + $crm_data['crm_confirmed'] + $crm_data['crm_prestart_attended'] + $crm_data['crm_not_attended'] + $crm_data['crm_date_started'] + $crm_data['crm_start_date_held'] + $crm_data['crm_invoiced'] + $crm_data['crm_disputed'] + $crm_data['crm_paid'];
        /*** /daily stats */
        return response()->json(['crm_data' => $crm_data, 'crm_total' => $crm_total]);
    }
    public function clientCVSData($source, $startDate, $endDate, $jobCategory)
    {
        if ($startDate === null) {
            $startDate = Carbon::now()->toDateString();
        }

        // Set end date to today if null
        if ($endDate === null) {
            $endDate = Carbon::now()->toDateString();
        }

        // Initialize variable to hold clients data
        $clients = [];
        $applicantSources = ['Total Jobs', 'Reed', 'Niche', 'CV Library', 'Social Media', 'Referral', 'Other Source'];
        if ($source == "NoSource") {
            // Fetch data from the clients table without filtering by source
            $clients = Client::where('app_job_category', $jobCategory)
                ->whereNotIn('app_source',$applicantSources)
                ->whereBetween('created_at', [$startDate, $endDate])

                ->get();
        } else {
            // Fetch data from the clients table based on job category and source
            $clients = Client::where('app_job_category', $jobCategory)
                ->where('app_source', $source)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
        }

        // Return the view with the fetched clients data
        return view('administrator.dashboard.clients_source', ['clients' => $clients,'source_name'=>$source]);
    }
    public function saleOpen(Request $request){
        $startDate=$request->start_date;
        $endDate=$request->end_date;
        return view('administrator.dashboard.open_sale', ['startDate' => $startDate,'endDate'=>$endDate]);

    }
    public function getOpenSales($startDate,$endDate, Request  $request){
        $sales=Sale::where('status','active')->whereBetween('created_at', [$startDate, $endDate]);
        if ($request->ajax()){
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
                    $user=User::where('id',$row->user_id)->first();
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
    public function saleClose(Request $request){
        $startDate=$request->close_start_date;
        $endDate=$request->close_end_date;
//        dd($request->all());
        return view('administrator.dashboard.close_sale', ['startDate' => $startDate,'endDate'=>$endDate]);

    }
    public function getCloseSales($startDate,$endDate, Request  $request){
        $sales=Sale::where('status','disable')->whereBetween('created_at', [$startDate, $endDate]);
//        dd($sales);
        if ($request->ajax()){
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
                    $user=User::where('id',$row->user_id)->first();
                    if (!empty($user)) {
                        $user_name = ucfirst($user->fullName);
                        return $user_name;
                    }else {
                        return 'null';
                    }
                })
                ->addColumn('job_category', function ($row) {
                    return $row->job_category ?? 'N/A';
                })
                ->addColumn('job_title', function ($row) {
                    if ($row->job_title_prof!=null){
                        $specialName=Specialist_job_titles::where('id',$row->job_title_prof)->first();
                        if (!empty($specialName)) {
                            return $row->job_title . ' (' . $specialName->name . ')';
                        }else{
                            return $row->job_title;
                        }
//                        return 'tes';
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

}
