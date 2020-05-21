<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    /**
     * Handle the team "created" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        $project->users()->attach(Auth::guard()->user());
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        $project->users()->detach();
        $project->languages()->detach();
    }
}
