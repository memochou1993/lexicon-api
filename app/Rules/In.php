<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class In implements Rule
{
    /**
     * @var array
     */
    private array $values;

    /**
     * Create a new rule instance.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return collect($value)->diff($this->values)->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $relations = collect($this->values)->map(function ($value) {
            return '"'.$value.'"';
        })->implode(', ');

        $values = Str::of($relations)->replaceLast(', ', ' and ');

        return trans('validation.relations', ['values' => $values]);
    }
}
