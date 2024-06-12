<?php

namespace App\Observers;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class ActionObserver
{
    public function changeSaleStatus($sale, $columns)
    {
        $auth_user = Auth::user();
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $data['action_performed_by'] = $auth_user->fullName;
        $data['changes_made'] = $columns;
        $d_message = 'opened';
        $message = 'sale-opened';
        if ($columns['status'] == 'disable') {
            $d_message = 'closed';
            $message = 'sale-closed';
        } elseif ($columns['status'] == 'rejected') {
            $d_message = 'rejected';
            $message = 'sale-rejected';
        }
        $data['message'] = 'Sale ('.$sale->postcode.' - '.$sale->job_title.') '.$d_message;

        $audit = new Audit();
        $audit->user_id = $auth_user->id;
        $audit->data = json_encode($data);
        $audit->message = $message;
        $audit->audit_added_date = $date;
        $audit->audit_added_time = $time;
        $audit->auditable_id = $sale->id;
        $audit->auditable_type = get_class($sale);
        $audit->save();
    }

}
