<?php

namespace App\Http\Requests\Traits;

trait HasPreparation
{
    /**
     * @param  string  $key
     * @return void
     */
    private function explode(string $key)
    {
        $this->merge([
            $key => $relations = collect($this->input($key))->explode(',')->toArray(),
        ]);
    }
}
