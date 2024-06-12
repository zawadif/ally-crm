<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RegisterRequest extends FormRequest
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
            'fullName' => 'required',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:MALE,FEMALE',
            'password' => 'required',
            'dob' => 'required|digits_between:7,15|numeric',
            'regionId' => 'required|integer',
            'phoneNumber' => 'required|unique:users,phoneNumber',
            'emergencyContactName' => 'required',
            'emergencyContactRelation' => 'required|in:FATHER,SPOUSE,BROTHER,GRANDPARENT,FRIEND,CHILD,MOTHER,SISTER,NEIGHBOR,GUARDIAN,WIFE,OTHER_RELATIVE,SIGNIFICANT_OTHER,DOCTOR',
            'emergencyContactNumber' => 'required',
            'fcmToken' => 'required',
            'deviceIdentifier' => 'required',
            'deviceName' => 'required',
            'deviceType' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required|string',
            'postalCode' => 'required|min:3|max:10|string',
            'avatar' => 'mimes:jpeg,bmp,png|dimensions:max_width=8000,max_height=8000',
            'completeAddress' => 'required'
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
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
