<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $url = $request->url();
            $explodedUrl = explode('/',$url);
            if(array_key_exists(3, $explodedUrl)){
                if($explodedUrl[3] == "webportal"){
                    return route('login',['type' => "WEBPORTAL"]);
                }                
            }
            return route('login');
        }
    }
}
