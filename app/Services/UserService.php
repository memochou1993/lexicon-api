<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserService
{
    /**
     * @var User
     */
    private User $user;

    /**
     * Instantiate a new service instance.
     *
     * @param  User  $user
     */
    public function __construct(
        User $user
    ) {
        $this->user = $user;
    }

    /**
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        return $this->user
            ->with($request->relations ?? [])
            ->paginate($request->per_page);
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     * @return Model
     */
    public function get(User $user, Request $request): Model
    {
        return $this->user
            ->with($request->relations ?? [])
            ->find($user->id);
    }

    /**
     * @param  User  $user
     * @param  array  $data
     * @return Model
     */
    public function update(User $user, array $data): Model
    {
        $user = $this->user->find($user->id);

        $user->update($data);

        return $user;
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $this->user->destroy($user->id);
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getTeams(User $user, Request $request): LengthAwarePaginator
    {
        return $user
            ->teams()
            ->with($request->relations ?? [])
            ->paginate($request->per_page);
    }

    /**
     * @param  User  $user
     * @param  array  $data
     * @return Model
     */
    public function storeTeam(User $user, array $data): Model
    {
        return $user->teams()->create($data);
    }

    /**
     * @param  User  $user
     * @param  array  $role_ids
     * @param  bool  $detaching
     */
    public function attachRole(User $user, array $role_ids, bool $detaching): void
    {
        $user->roles()->sync($role_ids, $detaching);
    }

    /**
     * @param  User  $user
     * @param  int  $role_id
     */
    public function detachRole(User $user, int $role_id): void
    {
        $user->roles()->detach($role_id);
    }
}
