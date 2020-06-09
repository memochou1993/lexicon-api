<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectLanguageStoreRequest extends FormRequest
{
    use HasPreparation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('update', $this->route('project'));

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Project $project */
        $project = $this->route('project');

        return [
            'language_ids' => [
                'array',
                'required',
                Rule::exists('languages', 'id')->where(function ($query) use ($project) {
                    $query->whereIn(
                        'id',
                        $project->getTeam()->languages->pluck('id')->toArray()
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
        $this->explode('language_ids');
    }
}
