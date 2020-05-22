<?php

namespace App\Http\Requests;

use App\Models\Key;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectKeyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('view', $this->route('project'));
        Gate::authorize('create', Key::class);

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
                Rule::unique('keys', 'name')->where(function ($query) {
                    $query->where('project_id', $this->route('project')->id);
                }),
            ],
        ];
    }
}
