<?php

namespace App\Services;

use App\Models\Role;
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
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Request  $request
     * @return Model
     */
    public function store(Request $request): Model
    {
        return $this->user->create($request->all());
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
     * @param  Request  $request
     * @return Model
     */
    public function update(User $user, Request $request): Model
    {
        $user->update($request->all());

        if ($request->role_ids) {
            $user->roles()->sync($request->role_ids);
        }

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
     * @param  array  $role_ids
     * @return array
     */
    public function attachRole(User $user, array $role_ids): array
    {
        return $user->roles()->syncWithoutDetaching($role_ids);
    }

    /**
     * @param  User  $user
     * @param  Role  $role
     * @return int
     */
    public function detachRole(User $user, Role $role): int
    {
        return $user->roles()->detach($role);
    }
}
