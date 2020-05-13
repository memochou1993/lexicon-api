<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamUserStoreRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TeamUserController extends Controller
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
     * @param  TeamUserStoreRequest  $request
     * @param  Team  $team
     * @return JsonResponse
     */
    public function store(TeamUserStoreRequest $request, Team $team)
    {
        $this->teamService->attachUser(
            $team,
            $request->user_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Team  $team
     * @param  User  $user
     * @return JsonResponse
     */
    public function destroy(Team $team, User $user)
    {
        $this->teamService->detachUser($team, $user->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
