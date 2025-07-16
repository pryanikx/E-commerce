<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @param LoginService $loginService
     * @param RegisterService $registerService
     */
    public function __construct(
        private readonly LoginService $loginService,
        private readonly RegisterService $registerService,
    ) {
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $this->registerService->register($request->validated());

        return response()->json(
            [
                'token' => $userData['token'],
                'user' => $userData['user'],
            ],
            200
        );
    }

    /**
     * Login an existing user/admin.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $userData = $this->loginService->login($request->validated());

            return response()->json([
                'token' => $userData['token'],
                'user' => $userData['user'],
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }
    }

    /**
     * Logout a user/admin.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => __('auth.unauthenticated')], 401);
        }

        $this->loginService->logout($user);

        return response()->json(['message' => __('messages.logout')]);
    }
}
