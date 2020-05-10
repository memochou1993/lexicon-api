<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUserStoreRequest extends FormRequest
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
            'sync' => [
                'bool',
            ],
            'user_ids' => [
                'array',
                'required',
                Rule::exists('users', 'id'),
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
        $this->prepareSync();
        $this->prepareUserIds();
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

    /**
     * @return void
     */
    private function prepareUserIds()
    {
        $this->merge([
            'user_ids' => collect($this->user_ids)->explode(',')->toArray(),
        ]);
    }
}
