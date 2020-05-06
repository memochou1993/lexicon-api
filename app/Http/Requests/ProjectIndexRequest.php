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
                    'users',
                    'team',
                    'languages',
                    'keys',
                    'values',
                    'values.languages',
                    'values.forms',
                ]),
            ],
            'team_id' => [
                'exists:teams,id',
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
        $relations = collect($this->relations)->explode(',');

        $relations->when($relations->contains('values'), function ($relations) {
            $relations->push('values.languages');
            $relations->push('values.forms');
        });

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
