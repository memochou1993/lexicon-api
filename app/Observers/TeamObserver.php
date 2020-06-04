<?php

namespace App\Observers;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class TeamObserver
{
    /**
     * Handle the team "created" event.
     *
     * @param  Team  $team
     * @return void
     */
    public function created(Team $team)
    {
        $team->users()->attach(Auth::user(), ['is_owner' => true]);
    }

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
