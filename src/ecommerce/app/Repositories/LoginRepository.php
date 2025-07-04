<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\LoginRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class LoginRepository implements LoginRepositoryInterface
{
    /**
     * login user
     *
     * @param array $credentials
     *
     * @return array
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
     * logout user;
     *
     * @param User $user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
