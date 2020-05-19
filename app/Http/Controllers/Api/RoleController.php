<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleIndexRequest;
use App\Http\Requests\RoleShowRequest;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource as Resource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    private RoleService $roleService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  RoleService  $roleService
     */
    public function __construct(
        RoleService $roleService
    ) {
        $this->authorizeResource(Role::class);

        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  RoleIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(RoleIndexRequest $request)
    {
        $roles = $this->roleService->getAll($request);

        return Resource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleStoreRequest $request
     * @return Resource
     */
    public function store(RoleStoreRequest $request)
    {
        $role = $this->roleService->store($request->all());

        return new Resource($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  RoleShowRequest  $request
     * @param  Role  $role
     * @return Resource
     */
    public function show(RoleShowRequest $request, Role $role)
    {
        $role = $this->roleService->get($role, $request);

        return new Resource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RoleUpdateRequest  $request
     * @param  Role  $role
     * @return Resource
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $role = $this->roleService->update($role, $request->all());

        return new Resource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role)
    {
        $this->roleService->destroy($role);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
