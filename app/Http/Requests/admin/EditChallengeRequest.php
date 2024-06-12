<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class EditChallengeRequest extends FormRequest
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
            'editladderId' => 'required|integer',
            'editdateTime' => 'required',
            'editmatchDate' => 'required',
            'editaddress' => 'required',
            'editlatitude' => 'required',
            'editlongitude' => 'required'
        ];
    }
}
