<?php

namespace App\Http\Requests;

use App\Rules\Relations;
use App\Support\Facades\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class KeyIndexRequest extends FormRequest
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
            'relations' => [
                new Relations([
                    'values',
                    'values.languages',
                    'values.forms',
                ]),
            ],
            'sort' => [
                Rule::in([
                    'name',
                ]),
            ],
            'direction' => [
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
            'per_page' => [
                'between:1,100',
                'numeric',
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
        $relations = Collection::make($this->input('relations'))
            ->explode(',')
            ->trim();

        if ($relations->contains('values')) {
            $relations = $relations->merge([
                'values.languages',
                'values.forms',
            ]);
        }

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
