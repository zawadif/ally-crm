<?php

namespace Database\Seeders;

use App\Enums\ConfigurationEnum;
use App\Enums\ServerEnum;
use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
 * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (env('APP_ENV') == ServerEnum::Staging) {
            $server = ServerEnum::Staging;
            $defaultValueArray = [
                false,   // Twilio configuration
            ];
        }else if(env('APP_ENV') == ServerEnum::Local){
            $server = ServerEnum::Local;
            $defaultValueArray = [
                false,   // Twilio configuration
            ];
        }else if(env('APP_ENV') == ServerEnum::Acceptance){
            $server = ServerEnum::Acceptance;
            $defaultValueArray = [
                false,   // Twilio configuration
            ];
        }else if(env('APP_ENV') == ServerEnum::Production){
            $server = ServerEnum::Production;
            $defaultValueArray = [
                true,   // Twilio configuration
            ];
        }

        $defaultCommentArray = [
            "Twilio configuration",   // Twilio configuration
        ];


        if (empty(Configuration::where('name', '=', 'TwilioConfiguration')->where('serverType',env('APP_ENV'))->first())) {
            $config = Configuration::create([
                'configName' => 'Twilio Configuration',
                'name' => 'TwilioConfiguration',
                'type' => ConfigurationEnum::ConfigBoolean,
                'value' => $defaultValueArray[0],
                'serverType' => $server,
                'comment' => $defaultCommentArray[0],
            ]);
        }
    }
}
