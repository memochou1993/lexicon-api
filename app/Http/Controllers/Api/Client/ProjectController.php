<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProjectIndexRequest;
use App\Http\Resources\Client\ProjectResource as Resource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
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
     * Display a listing of the resource.
     *
     * @param  ProjectIndexRequest  $request
     * @param  Project  $project
     * @return Resource
     */
    public function show(ProjectIndexRequest $request, Project $project)
    {
        $cacheKey = sprintf('projects:%s:keys', $project->id);

        $keys = Cache::rememberForever($cacheKey, function () use ($project, $request) {
            return $this->projectService->get($project, $request);
        });

        return new Resource($keys);
    }
}
