<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
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
        $user = $this->authService->storeUser($request);

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
     * @return JsonResponse
     */
    public function logout()
    {
        $this->authService->destroyTokens();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
