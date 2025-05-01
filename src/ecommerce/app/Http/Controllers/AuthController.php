<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected LoginService $loginService,
        protected RegisterService $registerService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDTO($request->validated());

        $result = $this->registerService->register($dto);

        return response()->json($result);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDTO($request->validated());

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
