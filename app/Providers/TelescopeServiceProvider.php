<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Telescope\EntryType;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::tag(function (IncomingEntry $entry) {
            $entryContent = $entry->content;
            if ($entry->type === EntryType::REQUEST) {
                $authUserTag = [];
                $completeBaseURLArray = explode("/",$entry->content['uri']);
                $baseURL = "/";
                if (array_key_exists(1, $completeBaseURLArray)) {
                    $baseURL .= $completeBaseURLArray[1];
                }
                $baseURL .= "/";
                if (array_key_exists(2, $completeBaseURLArray)) {
                    $baseURL .= $completeBaseURLArray[2];
                }
                $completeURLArray = explode("?",str_replace("api/v1/","",$entry->content['uri']));
                $varURL = "";
                if (array_key_exists("0",$completeURLArray)){
                    $varURL = $completeURLArray[0];

                    $checkidArray = explode("/",$completeURLArray[0]);
                    if(count($checkidArray)>0){
                        $varURL = "";
                        for($i=0;$i<count($checkidArray);$i++){
                            if($i != 0){
                                $varURL .= "/";
                            }                        
                            if(is_numeric($checkidArray[$i])){
                                $varURL .= "id";
                            }else{
                                $varURL .= $checkidArray[$i];
                            }
                        }
                    }
                }
                if (Auth::guard('sanctum')->check()) {
                    $authorizedUser = Auth::user();
                    if ($authorizedUser) {
                        $authUserTag = [
                            'Name:' . $authorizedUser->firstName,
                            'Mobile:' . $authorizedUser->phoneNumber,
                            'Url:'.$varURL,
                            'Baseurl:'.$baseURL,
                            'Auth-Url:'.$authorizedUser->id."-".$varURL
                        ];
                    }else{
                        $authUserTag = [
                            'Url:'.$varURL,
                            'Baseurl:'.$baseURL
                        ];
                    }
                }else{
                    $authUserTag = [
                        'Url:'.$varURL,
                        'Baseurl:'.$baseURL
                    ];
                    
                }
                return $authUserTag;
            }
            return [];
        });

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local') || $this->app->environment('staging') || $this->app->environment('acceptance') || $this->app->environment('production')) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->environment('local') || $this->app->environment('staging') || $this->app->environment('acceptance') || $this->app->environment('production')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'qa@techswivel.com'
            ]);
        });
    }
}
