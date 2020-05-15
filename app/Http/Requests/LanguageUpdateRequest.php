<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageUpdateRequest extends FormRequest
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
            'name' => [
                Rule::unique('languages', 'name')->where(function ($query) {
                    $query->whereIn(
                        'id',
                        $this->language->teams()->first()->languages()->pluck('id')->toArray()
                    );
                })->ignore($this->language->id),
            ],
        ];
    }
}
