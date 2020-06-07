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
            ->with($request->input('relations', []))
            ->orderBy($request->input('sort', 'id'), $request->input('direction', 'asc'))
            ->paginate($request->input('per_page'));
    }

    /**
     * @param  Role  $role
     * @param  Request  $request
     * @return Model|Role
     */
    public function get(Role $role, Request $request): Role
    {
        return $this->role
            ->with($request->input('relations', []))
            ->find($role->id);
    }

    /**
     * @param  Request  $request
     * @return Model|Role
     */
    public function store(Request $request): Role
    {
        /** @var Role $role */
        $role = $this->role->query()->create($request->all());

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->input('permission_ids'));
        }

        return $role;
    }

    /**
     * @param  Role  $role
     * @param  Request  $request
     * @return Model|Role
     */
    public function update(Role $role, Request $request): Role
    {
        $role->update($request->all());

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->input('permission_ids'));
        }

        return $role->withoutRelations();
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
