<?php

namespace App\Models\Traits;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

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

    /**
     * @param  string  $key
     * @return string
     */
    public function getSetting(string $key): string
    {
        return Arr::get($this->setting->settings, $key, '');
    }
}
