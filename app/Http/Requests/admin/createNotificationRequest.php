<?php

namespace App\Http\Requests\admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class createNotificationRequest extends FormRequest
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
        $rules = [
            'title' => 'required',
            'body' => 'required',
        ];
        if ($this->has('allDevice') || $this->has('androidDevice') || $this->has('iosDevice')) {
            if ($this->has('allDevice')) {
                $rules += ['allDevice' => Rule::requiredIf(!$this->androidDevice && !$this->iosDevice)];
            }

            if ($this->has('androidDevice')) {
                $rules += ['androidDevice' => Rule::requiredIf(!$this->allDevice && !$this->iosDevice)];
            }

            if ($this->has('iosDevice')) {
                $rules += ['iosDevice' => Rule::requiredIf(!$this->allDevice && !$this->androidDevice)];
            }
        } else {
            $rules += ['allDevice' => 'required'];
        }

        if ($this->has('allUser') || $this->has('freeUser') || $this->has('paidUser')) {
            if ($this->has('allUser')) {
                $rules += ['allUser' => Rule::requiredIf(!$this->freeUser && !$this->paidUser)];
            }

            if ($this->has('freeUser')) {
                $rules += ['freeUser' => Rule::requiredIf(!$this->paidUser && !$this->allUser)];
            }

            if ($this->has('paidUser')) {
                $rules += ['paidUser' => Rule::requiredIf(!$this->freeUser && !$this->allUser)];
            }
        } else {

            $rules += ['allUser' => 'required'];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'allDevice.required' => 'Notification device is required',
            'androidDevice.required' => 'Notification device is required.',
            'iosDevice.required' => 'Notification device is required',
            'allUser.required' => 'User Type is required',
            'paidUser.required' => 'User Type is required.',
            'freeUser.required' => 'User Type is required',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ]));
    }
}