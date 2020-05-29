<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectCacheController extends Controller
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
     * Remove the specified resource from the cache.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function destroy(Project $project)
    {
        $success = $this->projectService->destroyCached($project);

        return response()->api($success);
    }
}
