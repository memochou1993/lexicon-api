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
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Request  $request
     * @return Model
     */
    public function store(Request $request): Model
    {
        $role = $this->role->create($request->all());

        if ($request->permission_ids) {
            $role->permissions()->sync($request->permission_ids);
        }

        return $role;
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
     * @param  Request  $request
     * @return Model
     */
    public function update(Role $role, Request $request): Model
    {
        $role->update($request->all());

        if ($request->permission_ids) {
            $role->permissions()->sync($request->permission_ids);
        }

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
}
