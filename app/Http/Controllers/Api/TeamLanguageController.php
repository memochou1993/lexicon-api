<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamLanguageStoreRequest;
use App\Http\Requests\TeamLanguageIndexRequest;
use App\Http\Resources\LanguageResource as Resource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * Display a listing of the resource.
     *
     * @param  TeamLanguageIndexRequest  $request
     * @param  Team  $team
     * @return AnonymousResourceCollection
     */
    public function index(TeamLanguageIndexRequest $request, Team $team)
    {
        $language = $this->teamService->getLanguages(
            $team,
            $request->relations,
            $request->per_page,
        );

        return Resource::collection($language);
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
        $language = $this->teamService->storeLanguage($team, $request->all());

        return new Resource($language);
    }
}
