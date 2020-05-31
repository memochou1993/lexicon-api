<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamUserStoreRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

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
     * Assign the given user to the team.
     *
     * @param  TeamUserStoreRequest  $request
     * @param  Team  $team
     * @return JsonResponse
     */
    public function store(TeamUserStoreRequest $request, Team $team)
    {
        $changes = $this->teamService->attachUser($team, $request->user_ids);

        return response()->json([
            'success' => count($changes['attached']) > 0,
        ]);
    }

    /**
     * Revoke the given user from the team.
     *
     * @param  Team  $team
     * @param  User  $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Team $team, User $user)
    {
        $this->authorize('update', $team);

        $count = $this->teamService->detachUser($team, $user);

        return response()->json([
            'success' => $count > 0,
        ]);
    }
}
