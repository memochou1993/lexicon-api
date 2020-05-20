<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRoleStoreRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserRoleController extends Controller
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  UserService  $userService
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * Assign the given user to the role.
     *
     * @param  UserRoleStoreRequest  $request
     * @param  User  $user
     * @return JsonResponse
     */
    public function store(UserRoleStoreRequest $request, User $user)
    {
        $this->userService->attachRole(
            $user,
            $request->role_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Revoke the given user from the role.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(User $user, Role $role)
    {
        $this->authorize('update', $user);

        $this->userService->detachRole($user, $role->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
