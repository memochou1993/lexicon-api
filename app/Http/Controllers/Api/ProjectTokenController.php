<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Token;
use App\Services\ProjectService;
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
     */
    public function store(Project $project)
    {
        $token = $this->projectService->createToken($project);

        $payload = [
            'access_token' => $token,
        ];

        return response()->json($payload);
    }

    /**
     * @param  Project  $project
     * @param  Token  $token
     * @return JsonResponse
     */
    public function destroy(Project $project, Token $token)
    {
        $count = $this->projectService->destroyToken($project, $token);

        return response()->json([
            'success' => $count > 0,
        ]);
    }
}
