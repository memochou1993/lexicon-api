<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PermissionService
{
    /**
     * @var Permission
     */
    private Permission $permission;

    /**
     * Instantiate a new service instance.
     *
     * @param  Permission  $permission
     */
    public function __construct(
        Permission $permission
    ) {
        $this->permission = $permission;
    }

    /**
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        return $this->permission
            ->with($request->input('relations', []))
            ->orderBy($request->input('sort', 'id'), $request->input('direction', 'asc'))
            ->paginate($request->input('per_page'));
    }

    /**
     * @param  Permission  $permission
     * @param  Request  $request
     * @return Model|Permission
     */
    public function get(Permission $permission, Request $request): Permission
    {
        return $this->permission
            ->with($request->input('relations', []))
            ->find($permission->id);
    }
}
