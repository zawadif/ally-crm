<?php

namespace App\Providers;

use App\Models\User;
use App\Enums\RoleTypeEnum;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Enums\UserStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {
                $redirectURL = '/webportal';
                if (Session::has('url.intended')) {
                    $redirectURL = Session::get('url.intended');
                    Session::forget('url.intended');
                }
                if (Session::has('wrongPassword')) {
                    Session::forget('wrongPassword');
                }
                if (Session::has('paymentUrl')) {
                    Session::forget('paymentUrl');
                }
                if (auth()->user()->hasRole('user') and auth()->user()->role->count() == 1) {
                    if (Session::has('url.intended')) {
                        $redirectURL = Session::get('url.intended');
                        Session::forget('url.intended');
                        return redirect($redirectURL);
                    } else {
                        return redirect($redirectURL);
                    }
                } else if (auth()->user()->hasRole('user') and auth()->user()->role->count() > 1) {
                    if (strtolower($request->type) == 'webportal') {
                        if (Session::has('url.intended')) {
                            $redirectURL = Session::get('url.intended');
                            Session::forget('url.intended');
                            return redirect($redirectURL);
                        } else {
                            return redirect($redirectURL);
                        }
                    } else {
                        return redirect()->route('dashboard');
                    }
                } else {
                    return redirect()->route('dashboard');
                }
            }
        });
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                if (strtolower($request->type) == 'webportal') {
                    return redirect('/login?type=webportal');
                } else {
                    return redirect('/');
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        /* login auth rate limit/attempts */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email . $request->ip());
        });
        /* 2 factor auth rate limit/attempts */
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
        /* return your custom view for signin */
        Fortify::loginView(function () {
            return view('auth.login');
        });
        /* Login Attemp with email and password and it can be changed for phone number */
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->where('status','=','ACTIVE')->first();
            if ($user &&
                Hash::check($request->password, $user->password) ) {
                return $user;
            }
        });
        /* return your custom view for signup */

        /* custom password reset login */
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.passwords.email');
        });
        Fortify::resetPasswordView(function ($request) {
            return view('auth.passwords.reset', ['request' => $request]);
        });
        Fortify::verifyEmailView(function () {
            return view('auth.passwords.confirm');
        });
    }
}
