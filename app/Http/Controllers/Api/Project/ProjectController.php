<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectShowRequest;
use App\Http\Resources\Project\ProjectResource as Resource;
use App\Services\ProjectService;

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
     * @param  ProjectShowRequest  $request
     * @return Resource
     */
    public function show(ProjectShowRequest $request)
    {
        $project = $this->projectService->get(
            $request->user(),
            $request,
            $request->input('cached', false)
        );

        return new Resource($project);
    }
}
