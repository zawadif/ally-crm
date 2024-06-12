<?php

namespace App\Http\Requests\admin\role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class editAdminRole extends FormRequest
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
            'editRoleTitle' => 'required|string|max:30',
            'editSelectedPermission' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ]));
    }

    public function messages()
    {
        return [
            'editRoleTitle.required' => 'The role title field is required.',
            'editSelectedPermission.required' => 'Atleast one permission is required.',
        ];
    }
}
