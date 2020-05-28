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
        $success = $this->projectService->attachUser($project, $request->user_ids);

        return response()->api($success);
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

        $success = $this->projectService->detachUser($project, $user->id);

        return response()->api($success);
    }
}
