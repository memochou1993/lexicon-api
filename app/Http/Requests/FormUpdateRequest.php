<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormUpdateRequest extends FormRequest
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
                Rule::unique('forms', 'name')->where(function ($query) {
                    $query->whereIn(
                        'id',
                        $this->form->teams()->first()->forms()->pluck('id')->toArray()
                    );
                })->ignore($this->form->id),
            ],
        ];
    }
}
