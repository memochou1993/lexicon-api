<?php

namespace App\Http\Requests\Traits;

use App\Support\Facades\Collection;

trait HasPreparation
{
    /**
     * @param  string  $key
     * @return void
     */
    private function explode(string $key)
    {
        $this->merge([
            $key => Collection::make($this->input($key))->explode(',')->trim()->toArray(),
        ]);
    }
}
