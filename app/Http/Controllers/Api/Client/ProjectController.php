<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProjectShowRequest;
use App\Http\Resources\Client\ProjectResource as Resource;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;

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
        $project = $this->projectService->find(Auth::id());

        $keys = $this->projectService->getCached($project, $request);

        return new Resource($keys);
    }
}
