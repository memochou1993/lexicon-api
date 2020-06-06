<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HookUpdateRequest extends FormRequest
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
            'url' => [
                'min:1',
                'url',
                Rule::unique('hooks', 'url')->where(function ($query) {
                    $query->where('project_id', $this->route('hook')->project->id);
                })->ignore($this->route('hook')->id),
            ],
        ];
    }
}
