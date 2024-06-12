<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Validation\Rule;

class registerAdminUser extends FormRequest
{
    use PasswordValidationRules;
    
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
        $code = $this->request->get('phoneNumberCode');
        $emergencyCode = $this->request->get('emergencyPhoneNumberCode');
        return [
            'fullName' => 'required|string|max:30',
            'email' =>  'required|string|email|max:255',
            'password' => $this->passwordRules(),
            'confirmPassword' => 'required_with:password|same:password',
            'dateOfBirth' =>    'required|date_format:m/d/Y',
            'gender'=>  'required',
            'address' =>    'required|string|max:255',
            'region' => 'required',
            'state' =>  'required|string|max:30',
            'city' =>   'required|string|max:30',
            'country' =>    'required',
            'postalCode' => 'required|string|max:30',
            'phoneNumber' =>    'required|phone:'.$code.'|'.Rule::unique('user_details'),
            'phoneNumberCode' => 'required',
            'phoneNumberValue' => 'required',
            'emergencyFullName' =>  'required|string|max:30',
            'relationShip' =>   'required',
            'emergencyPhoneNumber' =>   'required|phone:'.$emergencyCode,
            'emergencyPhoneNumberCode' =>   'required',
            'emergencyPhoneNumberValue' =>   'required',
            'avatar' => 'image|mimes:jpg,png,jpeg|max:2048',
        ];
    }
}
