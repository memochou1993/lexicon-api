<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
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
                Rule::unique('projects', 'name')->where(function ($query) {
                    $query->where('team_id', $this->team_id);
                }),
            ],
            'team_id' => [
                'numeric',
                'required',
                Rule::exists('teams', 'id'),
            ],
        ];
    }
}
