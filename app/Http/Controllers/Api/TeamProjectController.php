<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamProjectIndexRequest;
use App\Http\Requests\TeamProjectStoreRequest;
use App\Http\Resources\ProjectResource as Resource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamProjectController extends Controller
{
    /**
     * @var TeamService
     */
    private TeamService $teamService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  TeamService  $teamService
     */
    public function __construct(
        TeamService $teamService
    ) {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  TeamProjectIndexRequest  $request
     * @param  Team  $team
     * @return AnonymousResourceCollection
     */
    public function index(TeamProjectIndexRequest $request, Team $team)
    {
        $projects = $this->teamService->getProjects(
            $team,
            $request->relations,
            $request->per_page
        );

        return Resource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TeamProjectStoreRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function store(TeamProjectStoreRequest $request, Team $team)
    {
        $project = $this->teamService->storeProject($team, $request->all());

        return new Resource($project);
    }
}
