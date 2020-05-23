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
            ->with($request->relations ?? [])
            ->paginate($request->per_page);
    }

    /**
     * @param  Permission  $permission
     * @param  Request  $request
     * @return Model
     */
    public function get(Permission $permission, Request $request): Model
    {
        return $this->permission
            ->with($request->relations ?? [])
            ->find($permission->id);
    }
}
