<?php

namespace App\Http\Requests\admin\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'otpType'=>'required|in:EMAIL,PHONE_NUMBER,FORGET_PASSWORD',
            'email'=>'required_if:otpType,EMAIL,FORGET_PASSWORD|email',
            'phoneNumber'=>'required_if:otpType,PHONE_NUMBER|string',
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
