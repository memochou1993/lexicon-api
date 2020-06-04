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
     * @param  Request  $request
     * @return Model
     */
    public function store(Request $request): Model
    {
        return $this->team->create($request->all());
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
     * @return array
     */
    public function attachUser(Team $team, array $user_ids): array
    {
        return $team->users()->syncWithoutDetaching($user_ids);
    }

    /**
     * @param  Team  $team
     * @param  User  $user
     * @return int
     */
    public function detachUser(Team $team, User $user): int
    {
        return $team->users()->detach($user);
    }
}
