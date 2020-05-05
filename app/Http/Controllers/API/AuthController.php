<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
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
     * @param  AuthRequest  $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(AuthRequest $request)
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
