<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    /**
     * @var Project
     */
    private Project $project;

    /**
     * @var Team
     */
    private Team $team;

    /**
     * Instantiate a new service instance.
     *
     * @param  Project  $project
     * @param  Team  $team
     */
    public function __construct(
        Project $project,
        Team $team
    ) {
        $this->project = $project;
        $this->team = $team;
    }

    /**
     * @param  int  $team_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getProjectsByTeamId(int $team_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->team->find($team_id)->projects()->with($relations)->paginate($per_page);
    }
}
