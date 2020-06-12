<?php

namespace App\Http\Requests;

use App\Rules\Relations;
use App\Support\Facades\Collection;
use Illuminate\Foundation\Http\FormRequest;

class KeyShowRequest extends FormRequest
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
                new Relations([
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
