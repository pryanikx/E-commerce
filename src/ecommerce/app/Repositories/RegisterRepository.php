<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;

class RegisterRepository implements RegisterRepositoryInterface
{
    /**
     * Register user
     *
     * @param array<string, string> $credentials
     *
     * @return array<string, string>
     */
    public function Register(array $credentials): array
    {
        $user = User::create($credentials);

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
