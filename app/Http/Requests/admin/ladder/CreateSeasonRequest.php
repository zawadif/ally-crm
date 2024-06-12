<?php

namespace App\Http\Requests\admin\ladder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSeasonRequest extends FormRequest
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
        $ladders =  explode(",", $this->selectedLadderIds);
        $field = array();
        foreach ($ladders as $ladder) {
            $field['Ladder'.$ladder] = 'required|numeric|between:1,100';
        }
        return $field;
    }
    public function messages()
    {
        $ladders =  explode(",", $this->selectedLadderIds);
        $field = array();
        foreach ($ladders as $ladder) {
            $field['Ladder'.$ladder.'.required'] = 'Enter price for ladder.';
            $field['Ladder'.$ladder.'.numeric'] = 'Price must be in number.';
            $field['Ladder'.$ladder.'.between'] = 'Each ladder price must be between 0 to 100 exclusively';
        }
        return $field;
    }
    protected function failedValidation(Validator $validator)
    { 
        $error = collect($validator->errors())->collapse()->toArray();
        $errors = implode(' | ', $error);
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $errors,
        ]));
    }

}
