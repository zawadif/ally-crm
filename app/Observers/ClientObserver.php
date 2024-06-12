<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function created(Client $applicant)
    {
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $applicant->audits()->create([
            "user_id" => Auth::id(),
            "data" => json_encode($applicant->toArray()),
            "message" => "Applicant {$applicant->app_name} has been created successfully at {$time} on {$date}",
            "audit_added_date" => $date,
            "audit_added_time" => $time
        ]);
    }

    /**
     * Handle the Client "updated" event.
     *
     * @param  \App\Models\Client  $applicant
     * @return void
     */
    public function updated(Client $applicant)
    {
        date_default_timezone_set('Europe/London');
        $date = date('jS F Y');
        $time = date("h:i A");

        $columns = $applicant->getDirty();
        $applicant['changes_made'] = $columns;

        $applicant->audits()->create([
            "user_id" => Auth::id(),
            "data" =>  json_encode($applicant->toArray()),
            "message" => "Applicant {$applicant->app_name} has been updated successfully at {$time} on {$date}",
            "audit_added_date" => $date,
            "audit_added_time" => $time
        ]);
    }

    /**
     * Handle the Client "deleted" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function deleted(Client $client)
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function restored(Client $client)
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function forceDeleted(Client $client)
    {
        //
    }
}
