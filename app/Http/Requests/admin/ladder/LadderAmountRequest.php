<?php

namespace App\Http\Requests\admin\ladder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Ladder;

class LadderAmountRequest extends FormRequest
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
        $seasonId = $this->seasonId;
        $ladders = Ladder::where('seasonId',$seasonId)->get();
        $field = array();
        foreach ($ladders as $ladder) {
            $field['editLadder'.$ladder->id] = 'required|numeric|between:0,99.99';
        }
        return $field;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ]));
    }
}
