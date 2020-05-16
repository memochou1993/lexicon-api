<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamLanguageStoreRequest;
use App\Http\Resources\LanguageResource as Resource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Auth\Access\AuthorizationException;

class TeamLanguageController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param TeamLanguageStoreRequest $request
     * @param Team $team
     * @return Resource
     * @throws AuthorizationException
     */
    public function store(TeamLanguageStoreRequest $request, Team $team)
    {
        $this->authorize('view', $team);

        $language = $this->teamService->storeLanguage($team, $request->all());

        return new Resource($language);
    }
}
