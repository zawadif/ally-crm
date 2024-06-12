<?php

namespace App\Http\Requests\Api;

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
            'otpType'=>'required|in:SIGNUP_NUMBER_OTP,SIGNUP_EMAIL_OTP,RESET_NUMBER_OTP,RESET_EMAIL_OTP',
            'email'=>'required_if:otpType,SIGNUP_EMAIL_OTP,RESET_EMAIL_OTP|email',
            'phoneNumber'=>'required_if:otpType,SIGNUP_NUMBER_OTP,RESET_NUMBER_OTP|string',
            'otp'=>'required|min:5|integer'
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

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $error = collect($validator->errors())->collapse()->toArray();
        $errors = implode(' | ', $error);
        throw new HttpResponseException(response()->json(
            ['response' => ['status' => false, 'message' => $errors]],
            Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
