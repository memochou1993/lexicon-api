<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectKeyStoreRequest;
use App\Http\Requests\ProjectKeyIndexRequest;
use App\Http\Resources\KeyResource as Resource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectKeyController extends Controller
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
     * @param  ProjectKeyIndexRequest  $request
     * @param  Project  $project
     * @return AnonymousResourceCollection
     */
    public function index(ProjectKeyIndexRequest $request, Project $project)
    {
        $keys = $this->projectService->getKeys($project, $request->all());

        return Resource::collection($keys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectKeyStoreRequest  $request
     * @param  Project  $project
     * @return Resource
     * @throws AuthorizationException
     */
    public function store(ProjectKeyStoreRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $key = $this->projectService->storekey($project, $request->all());

        return new Resource($key);
    }
}
