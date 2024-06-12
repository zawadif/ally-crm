<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'gender' => 'required|in:MALE,FEMALE',
            'dob' => 'required',
            'country' => 'required|string',
            'regionId' => 'required|integer',
            'emergencyContactName' => 'required',
            'emergencyContactRelation' => 'required|in:FATHER,SPOUSE,BROTHER,GRANDPARENT,FRIEND,CHILD,MOTHER,SISTER,NEIGHBOR,GUARDIAN,WIFE,DOCTOR,OTHER_RELATIVE,SIGNIFICANT_OTHER',
            'emergencyContactNumber' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postalCode' => 'required',
            'avatar' => 'sometimes|required|mimes:jpeg,bmp,png|dimensions:max_width=8000,max_height=8000',
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
