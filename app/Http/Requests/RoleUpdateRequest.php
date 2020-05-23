<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    use HasPreparation;

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
                'min:1',
                Rule::unique('roles', 'name')->ignore($this->route('role')->id),
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
        $this->explode('permission_ids');
    }
}
