<?php

namespace App\Http\Requests\Api\Challenges;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class GetChallenge extends FormRequest
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
            'categoryId' => 'required|numeric',
            'ladderId' => 'required|numeric',
            'filterBy'  => 'required|in:ALL,PENDING,WITHDRAWN,ACCEPTED,REJECTED',
            'type' => 'required|in:ALL,TO_ME,BY_ME'
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