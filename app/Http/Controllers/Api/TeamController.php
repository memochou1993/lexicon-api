<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamShowRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Http\Resources\TeamResource as Resource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
        $this->authorizeResource(Team::class);

        $this->teamService = $teamService;
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
