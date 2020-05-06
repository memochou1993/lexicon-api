<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
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

        return response()->json([
            'access_token' => $token,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function user()
    {
        $user = $this->authService->getUser();

        return response()->json([
            'data' => $user,
        ]);
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
