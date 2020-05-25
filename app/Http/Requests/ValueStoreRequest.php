<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ValueStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('view', $this->route('key'));

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
            'text' => [
                'required',
            ],
            'language_id' => [
                'numeric',
                'required',
                Rule::exists('languages', 'id'),
            ],
            'form_id' => [
                'numeric',
                'required',
                Rule::exists('forms', 'id'),
            ],
        ];
    }
}
