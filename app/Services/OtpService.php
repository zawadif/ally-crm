<?php


namespace App\Services;

use App\Enums\OtpTypes;
use App\Enums\UserStatusEnum;
use App\Http\Resources\User\UserResource;
use App\Mail\SendOtp;
use App\Models\FcmToken;
use App\Models\Otp;
use App\Models\User;
use App\Traits\PhoneNumberFormattingTraits;
use Facades\App\Services\FcmTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class OtpService
{
    use PhoneNumberFormattingTraits;


    public function __construct()
    {

    }
    public function getCoordinates($postcode)
    {
//         = 'L1 8JQ';
        $apiKey='AIzaSyCA1maGn_da4Y35faHXfCxa4sau-bYlSKk';
        $postcode1=urlencode($postcode);

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode1}&key={$apiKey}";
        $response = file_get_contents($url);
//
        try {

            $data = json_decode($response, true);
//            dd($data);
            if ($data['status'] === 'OK') {
                $latitude = $data['results'][0]['geometry']['location']['lat'];
                $longitude = $data['results'][0]['geometry']['location']['lng'];
//                dd($latitude,$longitude);
                return [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            } else {
                dd('error');
                return null;
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Handle exceptions if the request fails
            return null;
        }




//        return response()->json(['error' => 'Location not found for this postcode.'], 404);
    }

    public function validate($request)
    {
        $otpType = $request->otpType;
        if ($otpType == OtpTypes::SIGNUP_NUMBER_OTP) {
            return $this->signUpNumberOtp($request);
        }
        if ($otpType == OtpTypes::SIGNUP_EMAIL_OTP) {
            return $this->SignUpEmailOtp($request);
        }
        if ($otpType == OtpTypes::RESET_EMAIL_OTP) {
            return $this->resetPasswordEmailOtp($request);
        }
        if ($otpType == OtpTypes::RESET_NUMBER_OTP) {
            return $this->ResetNumberOtp($request);
        }
    }

    public function signUpNumberOtp($request)
    {
        try {
            $request->phoneNumber =$this->formattingPhone($request->phoneNumber);
            $otpCode = Otp::where('email_phoneNumber', $request->phoneNumber)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid.']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::SIGNUP_NUMBER_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function ResetNumberOtp($request)
    {
        try {
            $request->phoneNumber =$this->formattingPhone($request->phoneNumber);
            $otpCode = Otp::where('email_phoneNumber', $request->phoneNumber)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid.']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::RESET_NUMBER_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function SignUpEmailOtp($request)
    {
        try {
            $otpCode = Otp::where('email_phoneNumber', $request->email)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid.']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::SIGNUP_EMAIL_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function resetPasswordEmailOtp($request)
    {
        try {
            $otpCode = Otp::where('email_phoneNumber', $request->email)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid.']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::RESET_EMAIL_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
