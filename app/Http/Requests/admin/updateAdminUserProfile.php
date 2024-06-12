<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class updateAdminUserProfile extends FormRequest
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
        $userId = $this->request->get('userId');
        $code = $this->request->get('contactNumberCode');
        return [
            'userId' => 'required',
            'firstName' => 'required|string|max:30',
            'lastName' => 'nullable|string|max:30',
            'email' => 'required|string|email|max:255|' . Rule::unique('users')->ignore($userId),
            'gender' => 'required|string|max:30',
            'address' => 'required|string|max:1024',
            'country' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'postalCode' => 'required|string|max:50',
            // 'contactNumber' => 'required|phone:' . $code . '|' . Rule::unique('user_details'),
            'avatar' => 'image|mimes:jpg,png,jpeg|max:2048',
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
