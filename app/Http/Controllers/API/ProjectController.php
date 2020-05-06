<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\ProjectShowRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource as Resource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * @return AnonymousResourceCollection
     */
    public function index(ProjectIndexRequest $request)
    {
        $projects = $this->projectService->getByTeam(
            $request->team_id,
            $request->relations,
            $request->per_page
        );

        return Resource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(ProjectStoreRequest $request)
    {
        $project = $this->projectService->storeByTeam($request->team_id, $request->all());

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
        $project = $this->projectService->get($project, $request->relations);

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
        $project = $this->projectService->update($project, $request->all());

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
