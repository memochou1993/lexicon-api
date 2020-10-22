<?php

namespace App\Http\Requests;

use App\Models\Key;
use App\Rules\NotNumeric;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyUpdateRequest extends FormRequest
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
        /** @var Key $key */
        $key = $this->route('key');

        return [
            'name' => [
                'min:1',
                new NotNumeric(),
                Rule::unique('keys', 'name')
                    ->where('project_id', $key->getCachedProject()->id)
                    ->ignore($key->id),
            ],
        ];
    }
}
