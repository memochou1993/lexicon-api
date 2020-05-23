<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            ],
            'email' => [
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')->id),
            ],
            'password' => [
                'min:8',
            ],
            'role_ids' => [
                'array',
                Rule::exists('roles', 'id'),
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
        $this->explode('role_ids');
    }
}
