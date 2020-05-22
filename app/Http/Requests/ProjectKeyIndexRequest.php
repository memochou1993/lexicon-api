<?php

namespace App\Http\Requests;

use App\Rules\In;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ProjectKeyIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('view', $this->route('project'));

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
                    'values',
                    'values.languages',
                    'values.forms',
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

        $relations->when($relations->contains('values'), function ($relations) {
            $relations->push('values.languages');
            $relations->push('values.forms');
        });

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
