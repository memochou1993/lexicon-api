<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProjectIndexRequest;
use App\Http\Resources\ProjectResource as Resource;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
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
     * Display a listing of the resource.
     *
     * @param  UserProjectIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(UserProjectIndexRequest $request)
    {
        $teams = $this->userService->getProjects(Auth::guard()->user(), $request);

        return Resource::collection($teams);
    }
}
