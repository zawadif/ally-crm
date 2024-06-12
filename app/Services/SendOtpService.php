<?php


namespace App\Services;

use App\Enums\OtpTypes;
use App\Mail\ResetPassword;
use App\Mail\SendOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Traits\PhoneNumberFormattingTraits;

class SendOtpService
{
    use PhoneNumberFormattingTraits;

    protected $twilio;

    public function __construct()
    {
        $this->twilio = new TwilioService();
    }

    public function sendOtp($request)
    {
        $authType = $request->otpType;
        if ($authType == OtpTypes::SIGNUP_EMAIL_OTP) {
            return $this->sendSignUpEmailOtp($request);
        }
        if ($authType == OtpTypes::SIGNUP_NUMBER_OTP) {
            return $this->sendSignUpNumberOtp($request);
        }
        if ($authType == OtpTypes::RESET_EMAIL_OTP) {
            return $this->sendResetPasswordEmailOtp($request);
        }
        if ($authType == OtpTypes::RESET_NUMBER_OTP) {
            return $this->sendResetPasswordNumberOtp($request);
        }
    }

    public function sendSignUpEmailOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $email = $request->email;
            $user = User::where('email', $email)->first();
            if (!is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'User already registered with this email']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $email)->first();
            if (!is_null($otpVerfiy)) {
                try {
                    DB::beginTransaction();
                    Mail::to($email)->send(new SendOtp($otp));
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::SIGNUP_EMAIL_OTP,
                    ]);
                    DB::commit();
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                } catch (\Exception $e) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Mail could not be sended']], JsonResponse::HTTP_BAD_REQUEST);
                }

            }
            Otp::create([
                'email_phoneNumber' => $request->email,
                'otp' => $otp,
                'otpType' => OtpTypes::SIGNUP_EMAIL_OTP,
            ]);
            try {
                Mail::to($email)->send(new SendOtp($otp));
            } catch (\Exception $e) {
                return response()->json(['response' => ['status' => false, 'message' => 'Mail could not be sended']], JsonResponse::HTTP_BAD_REQUEST);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);

        }
    }

    public function sendResetPasswordEmailOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $user = User::where('email', $request->email)->first();
            if (is_null($user)) {
                // JsonResponse::HTTP_UNPROCESSABLE_ENTITY there was 422 i think its good
                // Unable to find user with given email
                return response()->json(['response' => ['status' => false, 'message' => 'No account registered for this email']], JsonResponse::HTTP_NOT_FOUND);
            }
            $email = $request->email;
            $otpVerfiy = Otp::where('email_phoneNumber', $email)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                Mail::to($email)->send(new ResetPassword($otp, $user));
                $otpVerfiy->update([
                    'otp' => $otp,
                    'otpType' => OtpTypes::RESET_EMAIL_OTP,
                ]);
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            Otp::create([
                'email_phoneNumber' => $request->email,
                'otp' => $otp,
                'otpType' => OtpTypes::RESET_EMAIL_OTP,
            ]);
            Mail::to($email)->send(new ResetPassword($otp, $user));
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function sendSignUpNumberOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $phoneNumber = $this->formattingPhone($request->phoneNumber);
            $user = User::where('phoneNumber', $request->phoneNumber)->first();
            if (!is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'User already registered with this phone number']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $phoneNumber)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                $message = $otp . " is your Tennis Fights verification code.";
                $msg = $this->twilio->sendMessage($message, $phoneNumber);
                if ($msg['status'] == true) {
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::SIGNUP_NUMBER_OTP,
                    ]);
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => 'The number is not a valid phone number']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            $message = $otp . " is your Tennis Fights verification code.";
            $msg = $this->twilio->sendMessage($message, $phoneNumber);
            if ($msg['status'] == true) {
                Otp::create([
                    'email_phoneNumber' => $phoneNumber,
                    'otp' => $otp,
                    'otpType' => OtpTypes::SIGNUP_NUMBER_OTP,
                ]);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'The  number is not a valid phone number']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
        }

    }
    public function sendResetPasswordNumberOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $phoneNumber = $this->formattingPhone($request->phoneNumber);
            $user = User::where('phoneNumber', $request->phoneNumber)->first();
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'No account registered for this phone number!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $phoneNumber)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                $message = $otp . " is your Tennis Fights reset password code.";
                $msg = $this->twilio->sendMessage($message, $phoneNumber);
                if ($msg['status'] == true) {
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::RESET_NUMBER_OTP,
                    ]);
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => 'The  number is not a valid phone number']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            $message = $otp . " is your Tennis Fights reset password code.";
            $msg = $this->twilio->sendMessage($message, $phoneNumber);
            if ($msg['status'] == true) {
                Otp::create([
                    'email_phoneNumber' => $phoneNumber,
                    'otp' => $otp,
                    'otpType' => OtpTypes::RESET_NUMBER_OTP,
                ]);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'The  number is not a valid phone number']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function generateOtp($request)
    {
        $otp = mt_rand(1000, 9999);
        if (env('APP_ENV') == 'staging' || env('APP_ENV') == 'acceptance' || env('APP_ENV') == 'local'){
            $otp = 1111;
        }
        return $otp;
    }
}
