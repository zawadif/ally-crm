<?php

namespace App\Http\Requests\Api\Match;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateMatchRequest extends FormRequest
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
            'matchId' => 'required|integer',
            'type' => 'required_if:status,REJECTED,ACCEPTED|in:PROPOSAL,CHALLENGE,PLAYOFF',
            'status' => 'required|in:ACCEPTED,REJECTED,CANCEL,SCORE_UPDATE,RESCHEDULE,ACCEPT_SCORE,UPDATE_COURT,WITHDRAWN',
            'firstTeamScore' => 'array|required_if:status,SCORE_UPDATE',
            'secondTeamScore' => 'array|required_if:status,SCORE_UPDATE',
            'matchTime' => 'required_if:status,RESCHEDULE',
            'matchDay' => 'required_if:status,RESCHEDULE',
            'firebaseMsgId' => 'required_if:status,RESCHEDULE,UPDATE_COURT',
            'address' => 'required_if:status,UPDATE_COURT',
            'latitude' => 'required_if:status,UPDATE_COURT',
            'longitude' => 'required_if:status,UPDATE_COURT',
            'reason' => 'required_if:status,CANCEL',
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
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
