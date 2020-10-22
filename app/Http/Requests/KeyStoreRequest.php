<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Rules\NotNumeric;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class KeyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('view', $this->route('project'));

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
            'name' => [
                'required',
                new NotNumeric(),
                Rule::unique('keys', 'name')
                    ->where('project_id', $project->id),
            ],
        ];
    }
}
