<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamStoreRequest extends FormRequest
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
                'required',
                Rule::unique('teams', 'name')
                    ->whereIn(
                        'id',
                        $user->teams()->where('is_owner', true)->pluck('id')->toArray()
                    ),
            ],
        ];
    }
}
