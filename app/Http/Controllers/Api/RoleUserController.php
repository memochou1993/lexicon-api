<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleUserStoreRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleUserController extends Controller
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
        $this->roleService = $roleService;
    }

    /**
     * Assign the given user to the role.
     *
     * @param  RoleUserStoreRequest  $request
     * @param  Role  $role
     * @return JsonResponse
     */
    public function store(RoleUserStoreRequest $request, Role $role)
    {
        $this->roleService->attachUser(
            $role,
            $request->user_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Revoke the given user from the role.
     *
     * @param  Role  $role
     * @param  User  $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role, User $user)
    {
        $this->authorize('update', $role);

        $this->roleService->detachUser($role, $user->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
