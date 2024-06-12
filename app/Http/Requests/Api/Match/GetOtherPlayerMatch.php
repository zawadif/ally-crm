<?php

namespace App\Http\Requests\Api\Match;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class GetOtherPlayerMatch extends FormRequest
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

            'page' => 'required|numeric',
            'limit' => 'required|numeric',
            'userId' => 'required|numeric',
        ];
    }
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