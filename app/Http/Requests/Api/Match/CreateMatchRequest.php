<?php

namespace App\Http\Requests\Api\Match;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateMatchRequest extends FormRequest
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
            'type'=> 'required|in:PROPOSAL,CHALLENGE',
            'challengeUserId'=> 'integer|required_if:type,CHALLENGE',
            'ladderId'=> 'required|integer',
            'category'=> 'required|integer',
            'matchDay'=> 'required',
            'matchTime'=> 'required',
            'address'=> 'required',
            'latitude'=> 'required',
            'longitude'=> 'required'
        ];
    }
    public function messages()
    {
        return [
            'type.required' => 'Type is required.',
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
            Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
