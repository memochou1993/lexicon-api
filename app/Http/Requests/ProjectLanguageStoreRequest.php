<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'exists:languages,id',
                'required',
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