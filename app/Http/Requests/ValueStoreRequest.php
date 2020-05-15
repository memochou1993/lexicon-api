<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValueStoreRequest extends FormRequest
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
            'text' => [
                'required',
            ],
            'key_id' => [
                'numeric',
                'required',
                Rule::exists('keys', 'id'),
            ],
            'language_id' => [
                'numeric',
                'required',
                Rule::exists('languages', 'id'),
            ],
            'form_id' => [
                'numeric',
                'required',
                Rule::exists('forms', 'id'),
            ],
        ];
    }
}
