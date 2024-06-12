<?php


namespace App\Services;

use App\Models\Configuration;
use App\Traits\loggerExceptionTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{
    use loggerExceptionTrait;
    function __construct()
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $this->twilio_number = getenv("TWILIO_NUMBER");
        $this->client = new Client($account_sid, $auth_token);
    }

    public function sendMessage($message, $recipients)
    {
        try {
            $twilioSwitch = Configuration::where('name','TwilioConfiguration')->first();
            if($twilioSwitch){
                $twilioSwitchValue = $twilioSwitch->value;
                if($twilioSwitchValue){
                    $this->client->messages->create($recipients,
                    ['from' => $this->twilio_number, 'body' => $message]);
                    $data = [
                        'status' => true,
                    ];
                }else{
                    $data = [
                        'status' => true,
                    ];
                }

            }
            return $data;

        } catch (\Exception $e) {
            $this->saveExceptionLog($e,'Twilio Exception');
            $data = [
                'status' => false,
                'Status-Code' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ];
            return $data;
        }
    }

}
