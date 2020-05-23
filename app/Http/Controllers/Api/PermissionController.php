<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionIndexRequest;
use App\Http\Requests\PermissionShowRequest;
use App\Http\Resources\PermissionResource as Resource;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PermissionController extends Controller
{
    /**
     * @var PermissionService
     */
    private PermissionService $permissionService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  PermissionService  $permissionService
     */
    public function __construct(
        PermissionService $permissionService
    ) {
        $this->authorizeResource(Permission::class);

        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  PermissionIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(PermissionIndexRequest $request)
    {
        $permissions = $this->permissionService->getAll($request);

        return Resource::collection($permissions);
    }

    /**
     * Display the specified resource.
     *
     * @param  PermissionShowRequest  $request
     * @param  Permission  $permission
     * @return Resource
     */
    public function show(PermissionShowRequest $request, Permission $permission)
    {
        $permission = $this->permissionService->get($permission, $request);

        return new Resource($permission);
    }
}
