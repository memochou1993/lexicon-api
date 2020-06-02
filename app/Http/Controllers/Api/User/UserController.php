<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserShowRequest;
use App\Http\Requests\Auth\UserUpdateRequest;
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
     * @param  UserUpdateRequest  $request
     * @return Resource
     */
    public function update(UserUpdateRequest $request)
    {
        $user = $this->userService->update(Auth::guard()->user(), $request);

        return new Resource($user);
    }
}
