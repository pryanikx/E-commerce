<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\User\LoginDTO;
use App\DTO\User\RegisterDTO;
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
        $requestValidated = $request->validated();

        $user = $this->registerService->register(
            new RegisterDTO(
                $requestValidated['name'],
                $requestValidated['email'],
                $requestValidated['password'],
                $requestValidated['role']
            )
        );

        return response()->json($user, 200);
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
            $requestValidated = $request->validated();

            $user = $this->loginService->login(
                new LoginDTO(
                    $requestValidated['email'],
                    $requestValidated['password'],
                )
            );

            return response()->json($user, 200);
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
