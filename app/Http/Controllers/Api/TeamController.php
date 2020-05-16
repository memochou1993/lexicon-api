<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamIndexRequest;
use App\Http\Requests\TeamShowRequest;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Http\Resources\TeamResource as Resource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
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
        $this->authorizeResource(Team::class, 'team');

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
        $teams = $this->teamService->getByUser(
            Auth::guard()->user()->id,
            $request->relations,
            $request->per_page
        );

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
        $team = $this->teamService->storeByUser(
            Auth::guard()->user()->id,
            $request->all()
        );

        return new Resource($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  TeamShowRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function show(TeamShowRequest $request, Team $team)
    {
        $team = $this->teamService->get($team, $request->relations);

        return new Resource($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TeamUpdateRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
        $team = $this->teamService->update($team, $request->all());

        return new Resource($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Team  $team
     * @return JsonResponse
     */
    public function destroy(Team $team)
    {
        $this->teamService->destroy($team);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
