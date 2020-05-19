<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoleService
{
    /**
     * @var Role
     */
    private Role $role;

    /**
     * Instantiate a new service instance.
     *
     * @param  Role  $role
     */
    public function __construct(
        Role $role
    ) {
        $this->role = $role;
    }

    /**
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        return $this->role
            ->with($request->relations ?? [])
            ->paginate($request->per_page);
    }

    /**
     * @param  array  $data
     * @return Model
     */
    public function store(array $data): Model
    {
        return $this->role->create($data);
    }

    /**
     * @param  Role  $role
     * @param  Request  $request
     * @return Model
     */
    public function get(Role $role, Request $request): Model
    {
        return $this->role
            ->with($request->relations ?? [])
            ->find($role->id);
    }

    /**
     * @param  Role  $role
     * @param  array  $data
     * @return Model
     */
    public function update(Role $role, array $data): Model
    {
        $role = $this->role->find($role->id);

        $role->update($data);

        return $role;
    }

    /**
     * @param  Role  $role
     * @return bool
     */
    public function destroy(Role $role): bool
    {
        return $this->role->destroy($role->id);
    }

    /**
     * @param  Role  $role
     * @param  array  $user_ids
     * @param  bool  $detaching
     */
    public function attachUser(Role $role, array $user_ids, bool $detaching): void
    {
        $role->users()->sync($user_ids, $detaching);
    }

    /**
     * @param  Role  $role
     * @param  int  $user_id
     */
    public function detachUser(Role $role, int $user_id): void
    {
        $role->users()->detach($user_id);
    }
}
