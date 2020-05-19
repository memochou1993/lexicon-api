<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource as Resource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
        $this->authorizeResource(User::class);

        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  UserIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(UserIndexRequest $request)
    {
        $users = $this->userService->getAll($request);

        return Resource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  UserShowRequest  $request
     * @param  User  $user
     * @return Resource
     */
    public function show(UserShowRequest $request, User $user)
    {
        $user = $this->userService->get($user, $request);

        return new Resource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserUpdateRequest  $request
     * @param  User  $user
     * @return Resource
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user = $this->userService->update($user, $request->all());

        return new Resource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $this->userService->destroy($user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
