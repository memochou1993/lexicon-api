<?php

namespace App\Http\Requests;

use App\Rules\In;
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
                'between:1,100',
                'numeric',
            ],
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
        $this->preparePerPage();
        $this->prepareRelations();
    }

    /**
     * @return void
     */
    private function preparePerPage()
    {
        $this->merge([
            'per_page' => $this->per_page ?? 10,
        ]);
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
