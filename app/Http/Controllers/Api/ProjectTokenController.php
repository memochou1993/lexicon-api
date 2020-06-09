<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Token;
use App\Services\ProjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class ProjectTokenController extends Controller
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
     * @param  Project  $project
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Project $project)
    {
        $this->authorize('update', $project);

        $token = $this->projectService->createToken($project);

        return response()->json([
            'access_token' => $token,
        ]);
    }

    /**
     * @param  Project  $project
     * @param  Token  $token
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Project $project, Token $token)
    {
        $this->authorize('update', $project);

        $count = $this->projectService->destroyToken($project, $token);

        $success = $count > 0;

        return response()->json([
            'success' => $success,
        ]);
    }
}
