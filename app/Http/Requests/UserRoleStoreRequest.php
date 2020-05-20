<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRoleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_ids' => [
                'array',
                'required',
                Rule::exists('roles', 'id'),
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
        $this->prepareRoleIds();
        $this->prepareSync();
    }

    /**
     * @return void
     */
    private function prepareRoleIds()
    {
        $this->merge([
            'role_ids' => collect($this->role_ids)->explode(',')->toArray(),
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
