<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyStoreRequest extends FormRequest
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
                Rule::unique('keys','name')->where(function ($query) {
                    $query->where('project_id', $this->project_id);
                }),
            ],
            'project_id' => [
                'numeric',
                'required',
                Rule::exists('projects', 'id'),
            ],
        ];
    }
}
