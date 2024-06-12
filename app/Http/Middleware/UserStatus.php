<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Models\Otp;
use App\Models\UserDetail;
use Auth;

class UserStatus
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
        $user = User::where('email', Auth::user()->email)->first();
        if($user->status == UserStatusEnum::BLOCK){
            $request->session()->flush();
            $request->session()->regenerateToken();
            return redirect()->guest('/login')->with('message', 'Your account is blocked from the dashboard');
        }
        else if($user->status == UserStatusEnum::REGISTRATION_INPROCESS){
            $emailVerify = false;
            $phoneNumberVerify = false;
            $otpEmail = Otp::where('email_phoneNumber',$user->email)->first();
            if($otpEmail){
                if($otpEmail->isVerified){
                    $emailVerify = true;
                }else{
                    return redirect('/user/otp/email/'.$user->id);
                }
            }
            else{
                return redirect('/user/otp/email/'.$user->id);
            }
                $userDetail = UserDetail::where('userId',$user->id)->first();
                if($userDetail){
                    $otpPhoneNumber = Otp::where('email_phoneNumber',$userDetail->phoneNumber)->first();
                    if($otpPhoneNumber){
                        if($otpPhoneNumber->isVerified){
                            $phoneNumberVerify = true;
                        }else{
                            return redirect('/user/otp/phoneNumber/'.$user->id);
                        }
                    }else{
                        return redirect('/user/otp/phoneNumber/'.$user->id);
                    }
                }else{
                    return redirect('/register/'.$user->id);
                }
            if($emailVerify and $phoneNumberVerify){
                return $next($request);    
            }
        }else{
            return $next($request);
        }
    }
}
