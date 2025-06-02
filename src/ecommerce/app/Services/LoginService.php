<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    /**
     * Login an existing user/admin.
     *
     * @param LoginDTO $dto
     *
     * @return array
     * @throws \Exception<string>
     */
    public function login(array $request_validated): array
    {
        $dto = new LoginDTO($request_validated);
        $credentials = $dto->toArray();

        if (!Auth::attempt($credentials)) {
            throw new \Exception(__('auth.failed'));
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Logout user/admin
     *
     * @param User $user
     *
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
