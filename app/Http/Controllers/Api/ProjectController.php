<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectShowRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Resources\ProjectResource as Resource;
use App\Models\Project;
use App\Models\Team;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

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
        $this->authorizeResource(Project::class);

        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ProjectIndexRequest  $request
     * @param  Team  $team
     * @return AnonymousResourceCollection
     */
    public function index(ProjectIndexRequest $request, Team $team)
    {
        $projects = $this->projectService->getByTeam($team, $request);

        return Resource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProjectStoreRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function store(ProjectStoreRequest $request, Team $team)
    {
        $project = $this->projectService->storeByTeam($team, $request);

        return new Resource($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  ProjectShowRequest  $request
     * @param  Project  $project
     * @return Resource
     */
    public function show(ProjectShowRequest $request, Project $project)
    {
        $project = $this->projectService->get($project, $request);

        return new Resource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectUpdateRequest  $request
     * @param  Project  $project
     * @return Resource
     */
    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $project = $this->projectService->update($project, $request);

        return new Resource($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function destroy(Project $project)
    {
        $this->projectService->destroy($project);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
