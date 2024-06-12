<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OPT;
use Auth;

class VerifyPhoneNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(User::where('email', Auth::user()->email)->exists()){
            $user = User::where('email', Auth::user()->email)->first();
            if($user->hasRole('admin')){
                return $next($request);
            }else{
                if(OPT::where('email_phoneNumber',$user->email)->exists()){
                    $opt = OPT::where('email_phoneNumber',$user->email)->first();  
                    if($opt->isVerified){
                        return $next($request);
                    }else{
                        return redirect('/user/email/otp');
                    }
                }else{
                    return redirect('/user/email/otp');
                }
            }
            
        }
        
    }
}
