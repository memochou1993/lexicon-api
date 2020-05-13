<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class TeamService
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var Team
     */
    private Team $team;

    /**
     * Instantiate a new service instance.
     *
     * @param  User  $user
     * @param  Team  $team
     */
    public function __construct(
        User $user,
        Team $team
    ) {
        $this->user = $user;
        $this->team = $team;
    }

    /**
     * @param  int  $user_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getByUser(int $user_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->user->find($user_id)->teams()->with($relations)->paginate($per_page);
    }

    /**
     * @param  int  $userId
     * @param  array  $data
     * @return Model
     */
    public function storeByUser(int $userId, array $data): Model
    {
        return $this->user->find($userId)->teams()->create($data);
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
