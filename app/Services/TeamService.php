<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        return $this->team
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function get(Team $team, Request $request): Model
    {
        return $this->team
            ->with($request->relations ?? [])
            ->find($team->id);
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function paginateByUser(User $user, Request $request): LengthAwarePaginator
    {
        return $user
            ->teams()
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     * @return Model
     */
    public function storeByUser(User $user, Request $request): Model
    {
        return $user->teams()->create($request->all());
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function update(Team $team, Request $request): Model
    {
        $team->update($request->all());

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
     * @return bool
     */
    public function attachUser(Team $team, array $user_ids): bool
    {
        $changes = $team->users()->syncWithoutDetaching($user_ids);

        return count($changes['attached']) > 0;
    }

    /**
     * @param  Team  $team
     * @param  int  $user_id
     * @return bool
     */
    public function detachUser(Team $team, int $user_id): bool
    {
        $detached = $team->users()->detach($user_id);

        return $detached > 0;
    }
}
