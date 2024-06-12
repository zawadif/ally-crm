<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class SendOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'otpType'=>'required|in:SIGNUP_NUMBER_OTP,SIGNUP_EMAIL_OTP,RESET_NUMBER_OTP,RESET_EMAIL_OTP',
            'email'=>'required_if:otpType,SIGNUP_EMAIL_OTP,RESET_EMAIL_OTP|email',
            'phoneNumber'=>'required_if:otpType,SIGNUP_NUMBER_OTP,RESET_NUMBER_OTP|string',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $error = collect($validator->errors())->collapse()->toArray();
        $errors = implode(' | ', $error);
        throw new HttpResponseException(response()->json(
            ['response' => ['status' => false, 'message' => $errors]],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
