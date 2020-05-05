<?php

namespace App\Http\Requests;

use App\Rules\In;
use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
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
            'per_page' => [
                'min:1',
                'numeric',
            ],
            'relations' => [
                new In([
                    'keys',
                    'languages',
                    'team',
                    'users',
                ]),
            ],
            'team_id' => [
                'numeric',
                'required',
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
        $this->merge([
            'per_page' => $this->per_page ?? 10,
            'relations' => collect($this->relations)->explode(',')->toArray(),
        ]);
    }
}
