<?php

namespace App\Observers;

use App\Models\Team;

class TeamObserver
{
    /**
     * Handle the project "deleted" event.
     *
     * @param  Team  $team
     * @return void
     */
    public function deleted(Team $team)
    {
        $team->users()->detach();
        $team->languages()->detach();
        $team->forms()->detach();
    }
}
