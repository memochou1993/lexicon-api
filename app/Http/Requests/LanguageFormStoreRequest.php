<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
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
        Gate::authorize('update', $this->route('language'));

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
            'sync' => [
                'bool',
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
        $this->prepareSync();
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

    /**
     * @return void
     */
    private function prepareSync()
    {
        $this->merge([
            'sync' => $this->sync ?? false,
        ]);
    }
}
