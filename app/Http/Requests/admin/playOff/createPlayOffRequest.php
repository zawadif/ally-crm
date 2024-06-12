<?php

namespace App\Http\Requests\admin\playOff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Ladder;

class createPlayOffRequest extends FormRequest
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
        $seasonId = $this->seasonIdPlayOff;
        $ladders = Ladder::where('seasonId',$seasonId)->get();
        $field = array();
        foreach ($ladders as $ladder) {
            $field['Ladder'.$ladder->id] = 'required|integer';
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
