<?php

namespace App\Http\Requests;

use App\Models\Form;
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
        /** @var Form $form */
        $form = $this->route('form');

        // TODO: optimizable
        return [
            'name' => [
                Rule::unique('forms', 'name')->where(function ($query) use ($form) {
                    $query->whereIn(
                        'id',
                        $form->getCachedTeam()->getCachedForms()->pluck('id')->toArray()
                    );
                })->ignore($form->id),
            ],
        ];
    }
}
