<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => [
                'min:1',
            ],
            'email' => [
                'email',
                Rule::unique('users', 'email')
                    ->ignore($user->email),
            ],
            'password' => [
                'min:8',
            ],
        ];
    }
}
