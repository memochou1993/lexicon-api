<?php

namespace App\Http\Requests;

use App\Models\Key;
use App\Models\Language;
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
        /** @var Key $key */
        $key = $this->route('key');

        /** @var Language $language */
        $language = Language::query()->findOrFail($this->input('language_id'));

        return [
            'language_id' => [
                'numeric',
                'required',
                Rule::in($key->getCachedProject()->languages->pluck('id')->toArray()),
            ],
            'form_id' => [
                'numeric',
                'required',
                Rule::in($language->forms->pluck('id')->toArray()),
            ],
        ];
    }
}
