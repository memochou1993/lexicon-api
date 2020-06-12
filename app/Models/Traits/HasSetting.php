<?php

namespace App\Models\Traits;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSetting
{
    /**
     * Get the model's setting.
     *
     * @return MorphMany
     */
    public function setting()
    {
        return $this->morphOne(Setting::class, 'model');
    }
}
