<?php

namespace App\Observers;

use App\Models\Office;
use Illuminate\Support\Facades\Auth;

class OfficeObserver
{
    /**
     * Handle the Office "created" event.
     *
     * @param  \App\Models\Office  $office
     * @return void
     */
    public function created(Office $office)
    {
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $office->audits()->create([
            "user_id" => Auth::id(),
            "data" => json_encode($office->toArray()),
            "message" => "Office {$office->name} has been created successfully at {$time} on {$date}",
            "audit_added_date" => $date,
            "audit_added_time" => $time
        ]);
    }

    /**
     * Handle the Office "updated" event.
     *
     * @param  \App\Models\Office  $office
     * @return void
     */
    public function updated(Office $office)
    {
        //
    }

    /**
     * Handle the Office "deleted" event.
     *
     * @param  \App\Models\Office  $office
     * @return void
     */
    public function deleted(Office $office)
    {
        //
    }

    /**
     * Handle the Office "restored" event.
     *
     * @param  \App\Models\Office  $office
     * @return void
     */
    public function restored(Office $office)
    {
        //
    }

    /**
     * Handle the Office "force deleted" event.
     *
     * @param  \App\Models\Office  $office
     * @return void
     */
    public function forceDeleted(Office $office)
    {
        //
    }
}
