<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageFormStoreRequest extends FormRequest
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
            'form_ids' => [
                'array',
                'required',
                Rule::exists('forms', 'id'),
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
        $this->prepareFormIds();
    }

    /**
     * @return void
     */
    private function prepareFormIds()
    {
        $this->merge([
            'form_ids' => collect($this->form_ids)->explode(',')->toArray(),
        ]);
    }
}
