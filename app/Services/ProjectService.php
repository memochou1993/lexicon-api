<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

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
    public function getByTeam(int $team_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->team->find($team_id)->projects()->with($relations)->paginate($per_page);
    }

    /**
     * @param  Project  $project
     * @param  array  $relations
     * @return Model
     */
    public  function get(Project $project, array $relations): Model
    {
        return $this->project->with($relations)->find($project->id);
    }

    /**
     * @param  int  $teamId
     * @param  array  $data
     * @return Model
     */
    public  function storeByTeam(int $teamId, array $data): Model
    {
        return $this->team->find($teamId)->projects()->create($data);
    }

    /**
     * @param  Project  $project
     * @param  array  $data
     * @return Model
     */
    public  function update(Project $project, array $data): Model
    {
        $project = $this->project->find($project->id);

        $project->update($data);

        return $project;
    }
}
