<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthUserShowRequest;
use App\Http\Requests\AuthUserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  AuthService  $authService
     */
    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * @param  AuthRegisterRequest  $request
     * @return UserResource
     */
    public function register(AuthRegisterRequest $request)
    {
        $user = $this->authService->storeUser($request->all());

        return new UserResource($user);
    }

    /**
     * @param  AuthLoginRequest  $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(AuthLoginRequest $request)
    {
        $token = $this->authService->getToken(
            $request->email,
            $request->password,
            $request->device
        );

        if (! $token) {
            throw new AuthenticationException();
        }

        $payload = [
            'access_token' => $token,
        ];

        return response()->json($payload);
    }

    /**
     * @param  AuthUserShowRequest  $request
     * @return UserResource
     */
    public function getUser(AuthUserShowRequest $request)
    {
        $user = $this->authService->getUser($request->all());

        return new UserResource($user);
    }

    /**
     * @param  AuthUserUpdateRequest  $request
     * @return UserResource
     */
    public function updateUser(AuthUserUpdateRequest $request)
    {
        $user = $this->authService->updateUser($request->all());

        return new UserResource($user);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        $this->authService->destroyTokens();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
