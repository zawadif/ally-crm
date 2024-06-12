<?php

namespace App\Http\Requests\Api;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreChatNotification extends FormRequest
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
            'matchId' => 'required_if:chatType,SUPPORT,USER_CHAT',
            'messageType' => 'required|in:TEXT_MESSAGE,RECONFIRM_COURT,RE_SCHEDULE_MATCH',
            'chatType' => 'required|in:SUPPORT,ADMIN,USER_CHAT'
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