<?php

namespace App\Http\Requests\admin\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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

            'firstName' => 'required',
            'gender' => 'required|in:MALE,FEMALE',
            'date' => 'required',
            'country' => 'required|string',
            'emergencyFirstName' => 'required',
            'relation' => 'required|in:FATHER,SPOUSE,BROTHER,GRANDPARENT,FRIEND,CHILD,MOTHER,SISTER,NEIGHBOR,GUARDIAN,WIFE,DOCTOR,OTHER_RELATIVE,SIGNIFICANT_OTHER',
            'emergencyContact' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postalCode' => 'required',
            'avatar' => 'sometimes|required|mimes:jpeg,jpg,png|dimensions:max_width=8000,max_height=8000',
            'address' => 'required'
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
