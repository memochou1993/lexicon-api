<?php

namespace App\Http\Requests;

use App\Rules\In;
use Illuminate\Foundation\Http\FormRequest;

class AuthUserShowRequest extends FormRequest
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
            'relations' => [
                new In([
                    'teams',
                    'projects',
                ]),
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
        $this->prepareRelations();
    }

    /**
     * @return void
     */
    private function prepareRelations()
    {
        $this->merge([
            'relations' => $relations = collect($this->relations)->explode(',')->toArray(),
        ]);
    }
}
