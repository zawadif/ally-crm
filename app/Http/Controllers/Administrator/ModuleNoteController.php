<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModuleNoteController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $input['module'] = filter_var($request->input('module'), FILTER_SANITIZE_STRING);
        $request->replace($input);
        $model_notes_history = [];

        $validator = Validator::make($request->all(), [
            'module' => "required|in:Office,Sale,Unit,Applicant",
            'module_key' => "required"
        ])->validate();

        $module_key = $request->input('module_key');
        $model_class = 'App\Models\\' . $request->input('module');

        $module_name = $request->input('module');

        $module_notes_name = strtolower($request->input('module')).'_notes';
        if ($module_name == "Unit") {
            $module_notes_name = strtolower($request->input('module')).'s_notes';
        } elseif($module_name == "Sale") {
            $module_notes_name = strtolower($request->input('module')).'_note';
        }

        $module_notes_history = User::join('module_notes', 'users.id','=','module_notes.user_id')
            ->select( 'users.fullName', 'module_notes.details','module_notes.updated_at')
            ->where(['module_notes.module_noteable_id' => $module_key, 'module_noteable_type' => $model_class])
            ->orderBy('module_notes.id', 'DESC')->get();
//        dd($module_notes_history);

        if ($module_name == 'Sale') {
            $model_notes_history = User::join('sales_notes', 'users.id', '=', 'sales_notes.user_id')
                ->select('users.fullName as username', 'sales_notes.sale_note as note','sales_notes.updated_at')
                ->where('sales_notes.sale_id', '=', $module_key)
                ->orderBy('sales_notes.created_at', 'desc')
                ->get()->toArray();
        } else {
            $model = $model_class::with('user')->find($request->input('module_key'));
            $model_notes_history[0]['username'] = $model->user->name;
            $model_notes_history[0]['note'] = $model->$module_notes_name;
            $model_notes_history[0]['updated_at'] = $model->updated_at;
        }

        $history_modal_body = view('administrator.partial.module_notes_history', compact('module_notes_history', 'model_notes_history'))->render();
        return $history_modal_body;
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
            $input = $request->all();
            $input['module'] = filter_var($input['module'], FILTER_SANITIZE_STRING);
            $input['details'] = filter_var($input['details'], FILTER_SANITIZE_STRING);
            $request->replace($input);

            $validator = Validator::make($request->all(), [
                'module' => "required|in:Office,Sale,Unit,Client",
                'module_key' => "required",
                'details' => "required|string",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $html = '<div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <span class="font-weight-semibold">'.$request->input('module').'</span> Note Could not Added
                </div>';

            $model_class = 'App\Models\\' . $request->input('module');
            $model = $model_class::find($request->input('module_key'));

            if ($model) {
                $module_note = $model->module_notes()->create([
                    'user_id' => Auth::id(),
                    'module_note_added_date' => date('jS F Y'),
                    'module_note_added_time' => date("h:i A"),
                    'details' => $request->input('details'),
                    'status' => 'active'
                ]);

                $last_inserted_module_note = $module_note->id;

                if ($last_inserted_module_note) {
                    $html = '<div class="alert alert-success border-0 alert-dismissible" id="alert_note'.$model->id.'">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span class="font-weight-semibold">'.$request->input('module').'</span> Note Added Successfully
                        </div>';
                    return redirect()->back();
                } else {
                    toastr()->error('Failed to create module note.');
                    return redirect()->back();
//                    throw new \Exception('Failed to create module note.');
                }
            } else {
                toastr()->error('Modal not found!.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            toastr()->error('Something.');
            return redirect()->back();
        }
    }


}
