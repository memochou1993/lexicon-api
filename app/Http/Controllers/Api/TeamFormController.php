<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamFormStoreRequest;
use App\Http\Resources\FormResource as Resource;
use App\Models\Team;
use App\Services\TeamService;

class TeamFormController extends Controller
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
     * @param  TeamFormStoreRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function store(TeamFormStoreRequest $request, Team $team)
    {
        $form = $this->teamService->storeForm($team, $request->all());

        return new Resource($form);
    }
}
