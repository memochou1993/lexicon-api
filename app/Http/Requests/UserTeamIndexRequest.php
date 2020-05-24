<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Rules\Relations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserTeamIndexRequest extends FormRequest
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
            'per_page' => [
                'between:1,100',
                'numeric',
            ],
            'relations' => [
                new Relations([
                    'users',
                    'projects',
                    'languages',
                    'forms',
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
