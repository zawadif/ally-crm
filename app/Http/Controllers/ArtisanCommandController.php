<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class ArtisanCommandController extends Controller
{
    public function indexArtisan()
    {
        return view('artisan');
    }
    
    public function configurationLogout(Request $request)
    {
        session()->forget('artisanSession');
        return redirect('artisan-login');
    }
    public function configurationPassword()
    {
        if(session()->get('artisanSession')){
            return redirect('artisan');
        }
        return view('artisanPassword');
    }
    public function checkConfigurationPassword(Request $request)
    {

        if($request->password == env("CONFIGURATION_PASSWORD")){
            session(['artisanSession' => true]);
            return redirect('artisan');
        }
        return redirect('artisan-login')->with('error',"Enter correct password.");
    }
    public function runCommand(Request $request){
        // dd($request);
        $key = $request->input('key');
        $commandName='php artisan command not found';
        if($key != "appSetup_key"){
            try {
                switch($key){
                    case 'migrate':
                        Artisan::call('migrate --force');
                        $commandName= "php artisan migrate command successfully ran.";
                        break;
                    case 'fresh':
                        if(env("APP_ENV") == 'production'){                            
                            return back()->with('error','Can not run this command.');
                        }
                        Artisan::call('migrate:fresh');
                        $commandName= "php artisan migrate:fresh command successfully ran.";
                        break;
                    case 'db_dump':
                        if(env("APP_ENV") == 'production'){                            
                            return back()->with('error','Can not run this command.');
                        }
                        Artisan::call('migrate:fresh');
                        Artisan::call('db:seed');
                        $commandName= "php artisan migrate:fresh and db:seed command successfully ran";
                        break;
                    case 'seed':
                        Artisan::call('db:seed');
                        $commandName= "php artisan db:seed command successfully ran.";
                        break;
                    case 'cache':
                        Artisan::call('cache:clear');
                        $commandName= "php artisan cache:clear command successfully ran";
                        break;
                    case 'optimize':                        
                        Artisan::call('optimize');
                        $commandName= "php artisan optimize command successfully ran";                        
                        break;
                    case 'config':
                        Artisan::call('config:clear');
                        $commandName= "php artisan config:clear command successfully ran";
                        break;
                }
                return back()->with('success',$commandName.'!');
            } catch (\Exception $e) {
                return back()->with('error',$e->getMessage().'!');
            }
        }else{
            return back()->with('error','Access Denied.');
        }
    }
    public function index(Request $request){
        $key = $request->input('key');
        $commandName='php artisan command not found';
        if($key != "appSetup_key"){
            try {
                switch($key){
                    case 'migrate':
                        Artisan::call('migrate --force');
                        $commandName= "php artisan migrate command successfully run";
                        break;
                    case 'fresh':
                        Artisan::call('migrate:fresh --force');
                        $commandName= "php artisan migrate:fresh command successfully run";
                        break;
                    case 'db_dump':
                        Artisan::call('migrate:fresh --force');
                        Artisan::call('db:seed --force');
                        $commandName= "php artisan migrate:fresh and db:seed command successfully run";
                        break;
                    case 'seed':
                        Artisan::call('db:seed --force');
                        $commandName= "php artisan db:seed command successfully run";
                        break;
                    case 'cache':
                        Artisan::call('cache:clear');
                        $commandName= "php artisan cache:clear command successfully run";
                        break;
                    case 'config':
                        Artisan::call('config:clear');
                        $commandName= "php artisan config:clear command successfully run";
                        break;
                    case 'optimize':
                        Artisan::call('optimize');
                        $commandName= "php artisan optimize command successfully run";
                        break;
                }
                echo $commandName;
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }else{
            echo "Access Denied";
        }
    }
}
