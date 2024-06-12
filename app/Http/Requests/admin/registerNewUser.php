<?php

namespace App\Http\Requests\admin;

use App\Rules\UniqueConcatenatedPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class registerNewUser extends FormRequest
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
        $code = $this->request->get('phoneNumberCode');
        $emergencyCode = $this->request->get('emergencyPhoneNumberCode');
        return [
            'fullName' => 'required|string|max:30',
            'dateOfBirth' =>    'required|date_format:m/d/Y',
            'gender'=>  'required',
            'address' =>    'required|string|max:255',
            'region' => 'required',
            'state' =>  'required|string|max:30',
            'city' =>   'required|string|max:30',
            'country' =>    'required',
            'postalCode' => 'required|string|max:30',
            'phoneNumber' =>   ['required', new UniqueConcatenatedPhoneNumber('phoneNumberValue', 'phoneNumber')] ,
            'phoneNumberCode' =>    'required',
            'phoneNumberValue' => 'required',
            'avatar' => 'image|mimes:jpg,png,jpeg|max:2048',
            'emergencyFullName' =>  'required|string|max:30',
            'relationShip' =>   'required',
            'emergencyPhoneNumber' =>   'required|phone:'.$emergencyCode,
            'emergencyPhoneNumberCode' =>   'required',
            'emergencyPhoneNumberValue' =>   'required',
            
        ];
    }
}
