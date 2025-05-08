<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected LoginService $loginService,
        protected RegisterService $registerService,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $dto = new RegisterDTO($validated);

        $result = $this->registerService->register($dto);

        return response()->json($result);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $dto = new LoginDTO($validated);

        try {
            $result = $this->loginService->login($dto);

            return response()->json($result);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->loginService->logout($request->user());

        return response()->json(['message' => 'Successfully logged out']);
    }
}
