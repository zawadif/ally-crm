<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ArtisanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->session()->get('artisanSession') && env('IS_CONFIGURATION_VISIBLE')){
            return $next($request);
        }
        else{
            return redirect('artisan-login')->with('error',"Enter password!!!");
        }
    }
}
