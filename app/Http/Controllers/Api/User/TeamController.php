<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTeamIndexRequest;
use App\Http\Requests\UserTeamStoreRequest;
use App\Http\Resources\TeamResource as Resource;
use App\Services\TeamService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

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
     * @param  UserTeamIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(UserTeamIndexRequest $request)
    {
        $teams = $this->teamService->getByUser(Auth::guard()->user(), $request);

        return Resource::collection($teams);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserTeamStoreRequest  $request
     * @return Resource
     */
    public function store(UserTeamStoreRequest $request)
    {
        $team = $this->teamService->storeByUser(Auth::guard()->user(), $request);

        return new Resource($team);
    }
}
