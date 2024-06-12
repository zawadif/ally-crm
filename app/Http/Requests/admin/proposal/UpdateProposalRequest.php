<?php

namespace App\Http\Requests\admin\proposal;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProposalRequest extends FormRequest
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
            'editLadderId' => 'required|integer',
            'editMatchTime' => 'required',
            'editMatchDate' => 'required',
            'editProposalBy' => 'required',
            'editAddress' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ]));
    }
    public function messages()
    {
        return [
            'editLadderId.required' => 'The ladder field is required.',
            'editMatchTime.required' => 'The time field is required.',
            'editMatchDate.required' => 'The date field is required.',
            'editProposalBy.required' => 'The proposal field is required.',
            'editAddress.required' => 'The address field is required.',
        ];
    }
}
