<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectLanguageStoreRequest extends FormRequest
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
            'language_ids' => [
                'array',
                'required',
                Rule::exists('languages', 'id'),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->prepareLanguageIds();
    }

    /**
     * @return void
     */
    private function prepareLanguageIds()
    {
        $this->merge([
            'language_ids' => collect($this->language_ids)->explode(',')->toArray(),
        ]);
    }
}
