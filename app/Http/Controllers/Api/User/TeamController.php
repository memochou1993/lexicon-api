<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTeamIndexRequest;
use App\Http\Requests\UserTeamStoreRequest;
use App\Http\Resources\TeamResource as Resource;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  UserService  $userService
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  UserTeamIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(UserTeamIndexRequest $request)
    {
        $teams = $this->userService->getTeams(
            Auth::guard()->user(),
            $request->relations,
            $request->per_page
        );

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
        $team = $this->userService->storeTeam(Auth::guard()->user(), $request->all());

        return new Resource($team);
    }
}
