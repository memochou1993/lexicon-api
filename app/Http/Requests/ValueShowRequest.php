<?php

namespace App\Http\Requests;

use App\Rules\In;
use Illuminate\Foundation\Http\FormRequest;

class ValueShowRequest extends FormRequest
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
                    'languages',
                    'forms',
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
        $relations = collect($this->relations)->explode(',');

        $relations->push('languages');
        $relations->push('forms');

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
