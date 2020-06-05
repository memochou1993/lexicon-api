<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\HasPreparation;
use App\Rules\Relations;
use App\Support\Facades\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectIndexRequest extends FormRequest
{
    use HasPreparation;

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
                    'owners',
                    'users',
                    'languages',
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
        $relations = Collection::make($this->input('relations'))
            ->explode(',')
            ->trim();

        if ($relations->contains('owner')) {
            $relations = $relations->map(function ($item) {
                return $item === 'owner' ? 'owners' : $item;
            });
        }

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
