<?php

namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Support\Facades\Gate;
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
        /** @var Team $team */
        $team = $this->route('team');

        // TODO: optimizable
        return [
            'name' => [
                'required',
                Rule::unique('forms', 'name')->where(function ($query) use ($team) {
                    $query->whereIn(
                        'id',
                        $team->getCachedForms()->pluck('id')->toArray()
                    );
                }),
            ],
            'range_min' => [
                'numeric',
                'required',
            ],
            'range_max' => [
                'numeric',
                'required',
            ],
        ];
    }
}
