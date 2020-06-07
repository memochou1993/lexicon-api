<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LanguageStoreRequest extends FormRequest
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
        /** @var Team $team */
        $team = $this->route('team');

        // TODO: optimizable
        return [
            'name' => [
                'required',
                Rule::unique('languages', 'name')->where(function ($query) use ($team) {
                    $query->whereIn(
                        'id',
                        $team->getCachedLanguages()->pluck('id')->toArray()
                    );
                }),
            ],
            'form_ids' => [
                'array',
                Rule::exists('forms', 'id')->where(function ($query) use ($team) {
                    $query->whereIn(
                        'id',
                        $team->getCachedForms()->pluck('id')->toArray()
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
