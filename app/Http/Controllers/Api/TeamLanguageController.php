<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamLanguageStoreRequest;
use App\Http\Resources\LanguageResource as Resource;
use App\Models\Team;
use App\Services\TeamService;

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
     * @param  TeamLanguageStoreRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function store(TeamLanguageStoreRequest $request, Team $team)
    {
        $language = $this->teamService->storeLanguage($team, $request);

        return new Resource($language);
    }
}
