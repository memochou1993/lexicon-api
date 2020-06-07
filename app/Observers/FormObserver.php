<?php

namespace App\Observers;

use App\Models\Form;

class FormObserver
{
    /**
     * Handle the form "deleted" event.
     *
     * @param  Form  $form
     * @return void
     */
    public function deleted(Form $form)
    {
        $form->getCachedTeam()->forgetCachedForms();
    }
}
