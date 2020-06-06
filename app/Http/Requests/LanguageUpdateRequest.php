<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageUpdateRequest extends FormRequest
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
            'name' => [
                Rule::unique('languages', 'name')->where(function ($query) {
                    $query->whereIn(
                        'id',
                        $this->route('language')->teams()->first()->languages()->pluck('id')->toArray() // TODO: should cache
                    );
                })->ignore($this->route('language')->id),
            ],
            'form_ids' => [
                'array',
                Rule::exists('forms', 'id')->where(function ($query) {
                    $query->whereIn(
                        'id',
                        $this->route('language')->teams()->first()->forms()->pluck('id')->toArray() // TODO: should cache
                    );
                }),
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
        $this->explode('form_ids');
    }
}
