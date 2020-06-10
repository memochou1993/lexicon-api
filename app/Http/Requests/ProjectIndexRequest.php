<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Rules\Relations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
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
        Gate::authorize('view', $this->route('team'));

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
        $this->explode('relations');
    }
}
