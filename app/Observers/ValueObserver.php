<?php

namespace App\Observers;

use App\Models\Value;

class ValueObserver
{
    /**
     * Handle the value "deleted" event.
     *
     * @param  Value  $value
     * @return void
     */
    public function deleted(Value $value)
    {
        $value->languages()->detach();
        $value->forms()->detach();
    }
}
