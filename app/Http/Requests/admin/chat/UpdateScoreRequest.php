<?php

namespace App\Http\Requests\admin\chat;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateScoreRequest extends FormRequest
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
            "teamOneSetOneScore"=>"nullable|integer",
            "teamOneSetTwoScore"=>"nullable|integer",
            "teamOneSetThreeScore"=>"nullable|integer",
            "teamTwoSetOneScore"=>"nullable|integer",
            "teamTwoSetTwoScore"=>"nullable|integer",
            "teamTwoSetThreeScore"=>"nullable|integer",
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
