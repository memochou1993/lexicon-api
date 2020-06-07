<?php

namespace App\Observers;

use App\Models\Language;

class LanguageObserver
{
    /**
     * Handle the language "deleted" event.
     *
     * @param  Language  $language
     * @return void
     */
    public function deleted(Language $language)
    {
        $language->forms()->detach();

        $language->getCachedTeam()->forgetCachedLanguages();
    }
}
