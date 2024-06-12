<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RequestPaymentRequest extends FormRequest
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
            'ladderId' => 'required|integer',
            'partnerId' => 'required_if:requestType,USER_ID',
            'partnerEmail' => 'required_if:requestType,USER_EMAIL',
            'requestType' => 'required|in:USER_EMAIL,USER_ID'
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