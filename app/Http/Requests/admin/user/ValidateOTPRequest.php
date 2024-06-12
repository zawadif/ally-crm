<?php

namespace App\Http\Requests\admin\user;

use App\Enums\OtpTypes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ValidateOTPRequest extends FormRequest
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
    public function rules(): array
    {
        $rules = [
            'otpType'=>'required|in:EMAIL,PHONE_NUMBER,FORGET_PASSWORD',
            'email'=>'required_if:otpType,EMAIL,FORGET_PASSWORD|email',
            'phoneNumber'=>'required_if:otpType,PHONE_NUMBER|string',
            'otp.*'=>'required|min:1|integer'
        ];
        return $rules;
    }

    /**
     * Get the error messages that apply to the request parameters.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phoneNumber.required' => 'Phone Number is required',
            'email.required' => 'Email is required.',
            'otp.required' => 'OTP Code is required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ]));
    }
}
