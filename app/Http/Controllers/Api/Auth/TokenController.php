<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TokenStoreRequest;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller
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
     * @param  TokenStoreRequest  $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function store(TokenStoreRequest $request)
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
    public function destroy()
    {
        $this->authService->destroyTokens();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
