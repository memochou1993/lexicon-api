<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleStoreRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('roles', 'name'),
            ],
            'permission_ids' => [
                'array',
                Rule::exists('permissions', 'id'),
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
        $this->preparePermissionIds();
    }

    /**
     * @return void
     */
    private function preparePermissionIds()
    {
        $this->merge([
            'permission_ids' => collect($this->permission_ids)->explode(',')->toArray(),
        ]);
    }
}
