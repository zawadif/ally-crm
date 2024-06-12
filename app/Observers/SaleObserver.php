<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SaleObserver
{
    public function created(Sale $sale)
    {
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $sale->audits()->create([
            "user_id" => Auth::id(),
            "data" => json_encode($sale->toArray()),
            "message" => "Sale {$sale->job_title} has been created successfully at {$time} on {$date}",
            "audit_added_date" => $date,
            "audit_added_time" => $time
        ]);
    }
    public function updated(Sale $sale, $message = null, $col = null)
    {
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $columns = $sale->getDirty();
        $sale['changes_made'] = $col == null ? $columns : $col;;

        $sale->audits()->create([
            "user_id" => Auth::id(),
            "data" => json_encode($sale->toArray()),
            "message" => $message == null ? "Sale {$sale->job_title} has been updated successfully at {$time} on {$date}" : $message . " at {$time} on {$date}",
            "audit_added_date" => $date,
            "audit_added_time" => $time
        ]);
    }

}
