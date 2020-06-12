<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        $project->users()->attach(Auth::user(), ['is_owner' => true]);

        $project->setting()->create([
            'settings' => [
                'secret_key' => Str::random(40),
            ],
        ]);
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
