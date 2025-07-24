<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class LoginService
{
    /**
     * @param AuthFactory $auth
     */
    public function __construct(
        private readonly AuthFactory $auth
    ) {
    }

    /**
     * Login an existing user/admin.
     *
     * @param array<string, string> $requestValidated
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function login(array $requestValidated): array
    {
        if (!$this->auth->guard()->attempt($requestValidated)) {
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
     * Logout user/admin.
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
