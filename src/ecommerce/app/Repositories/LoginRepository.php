<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\LoginRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class LoginRepository implements LoginRepositoryInterface
{
    /**
     * Login user.
     *
     * @param array<string, string> $credentials
     *
     * @return array<string, string>
     * @throws \Exception
     */
    public function login(array $credentials): array
    {
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
     * Logout user.
     *
     * @param User $user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
