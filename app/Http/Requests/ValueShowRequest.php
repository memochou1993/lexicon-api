<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Rules\Relations;
use App\Support\Facades\Collection;
use Illuminate\Foundation\Http\FormRequest;

class ValueShowRequest extends FormRequest
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
        $relations = Collection::make($this->input('relations'))
            ->explode(',')
            ->trim()
            ->merge([
                'languages',
                'forms',
            ]);

        $this->merge([
            'relations' => $relations->toArray(),
        ]);
    }
}
