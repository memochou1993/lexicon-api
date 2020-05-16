<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class TeamService
{
    /**
     * @var Team
     */
    private Team $team;

    /**
     * Instantiate a new service instance.
     *
     * @param  Team  $team
     */
    public function __construct(
        Team $team
    ) {
        $this->team = $team;
    }

    /**
     * @param  Team  $team
     * @param  array  $relations
     * @return Model
     */
    public function get(Team $team, array $relations): Model
    {
        return $this->team->with($relations)->find($team->id);
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @return Model
     */
    public function update(Team $team, array $data): Model
    {
        $team = $this->team->find($team->id);

        $team->update($data);

        return $team;
    }

    /**
     * @param  Team  $team
     * @return bool
     */
    public function destroy(Team $team): bool
    {
        return $this->team->destroy($team->id);
    }

    /**
     * @param  Team  $team
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getProjects(Team $team, array $relations, int $per_page): LengthAwarePaginator
    {
        return $team->projects()->with($relations)->paginate($per_page);
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @return Model
     */
    public function storeProject(Team $team, array $data): Model
    {
        return $team->projects()->create($data);
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @return Model
     */
    public function storeLanguage(Team $team, array $data): Model
    {
        return $team->languages()->create($data);
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @return Model
     */
    public function storeForm(Team $team, array $data): Model
    {
        return $team->forms()->create($data);
    }

    /**
     * @param  Team  $team
     * @param  array  $user_ids
     * @param  bool  $sync
     */
    public function attachUser(Team $team, array $user_ids, bool $sync): void
    {
        $team->users()->sync($user_ids, $sync);
    }

    /**
     * @param  Team  $team
     * @param  int  $user_id
     */
    public function detachUser(Team $team, int $user_id): void
    {
        $team->users()->detach($user_id);
    }
}
