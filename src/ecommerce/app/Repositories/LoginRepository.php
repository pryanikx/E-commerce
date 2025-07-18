<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\LoginRepositoryInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class LoginRepository implements LoginRepositoryInterface
{
    public function __construct(
        private readonly AuthFactory $auth
    ) {
    }

    /**
     * Login a user.
     *
     * @param array<string, string> $credentials
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function login(array $credentials): array
    {
        if (!$this->auth->guard()->attempt($credentials)) {
            return [
                'token' => null,
                'user' => null,
            ];
        }

        $user = $this->auth->guard()->user();

        if (!$user) {
            return [
                'token' => null,
                'user' => null,
            ];
        }

        $token = $user->createToken('token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Logout a user.
     *
     * @param User $user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
