<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TeamIndexRequest;
use App\Http\Requests\User\TeamStoreRequest;
use App\Http\Resources\TeamResource as Resource;
use App\Services\TeamService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller
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
     * @param  TeamIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(TeamIndexRequest $request)
    {
        $teams = $this->teamService->paginateByUser($request->user(), $request);

        return Resource::collection($teams);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TeamStoreRequest  $request
     * @return Resource
     */
    public function store(TeamStoreRequest $request)
    {
        $team = $this->teamService->store($request->user(), $request);

        return new Resource($team);
    }
}
