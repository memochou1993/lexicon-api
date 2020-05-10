<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectUserStoreRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @param  ProjectUserStoreRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function store(ProjectUserStoreRequest $request, Project $project)
    {
        $this->projectService->attachUser(
            $project,
            $request->user_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Project  $project
     * @param  User  $user
     * @return JsonResponse
     */
    public function destroy(Project $project, User $user)
    {
        $this->projectService->detachUser($project, $user->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
