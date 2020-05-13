<?php

namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormStoreRequest extends FormRequest
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
                'required',
                Rule::unique('forms', 'name')->where(function ($query) {
                    $query->whereIn(
                        'id',
                        Team::findOrNew($this->team_id)->forms()->pluck('id')->toArray()
                    );
                }),
            ],
            'team_id' => [
                'numeric',
                'required',
                Rule::exists('teams', 'id'),
            ],
        ];
    }
}
