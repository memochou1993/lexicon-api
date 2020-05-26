<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserShowRequest;
use App\Http\Requests\AuthUserUpdateRequest;
use App\Http\Resources\UserResource as Resource;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
     * @param  UserShowRequest  $request
     * @return Resource
     */
    public function show(UserShowRequest $request)
    {
        $user = $this->userService->get(Auth::guard()->user(), $request);

        return new Resource($user);
    }

    /**
     * @param  AuthUserUpdateRequest  $request
     * @return Resource
     */
    public function update(AuthUserUpdateRequest $request)
    {
        $user = $this->userService->update(Auth::guard()->user(), $request);

        return new Resource($user);
    }
}
