<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectUserStoreRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class ProjectUserController extends Controller
{
    /**
     * @var ProjectService
     */
    private ProjectService $projectService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  ProjectService  $projectService
     */
    public function __construct(
        ProjectService $projectService
    ) {
        $this->projectService = $projectService;
    }

    /**
     * Assign the given user to the project.
     *
     * @param  ProjectUserStoreRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function store(ProjectUserStoreRequest $request, Project $project)
    {
        $changes = $this->projectService->attachUser($project, $request->input('user_ids'));

        $success = count($changes['attached']) > 0;

        return response()->json([
            'success' => $success,
        ]);
    }

    /**
     * Revoke the given user from the project.
     *
     * @param  Project  $project
     * @param  User  $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Project $project, User $user)
    {
        $this->authorize('update', $project);

        if ($project->getCachedOwner()->is($user)) {
            abort(422, __('validation.in', ['attribute' => 'user']));
        }

        $count = $this->projectService->detachUser($project, $user);

        $success = $count > 0;

        return response()->json([
            'success' => $success,
        ]);
    }
}
