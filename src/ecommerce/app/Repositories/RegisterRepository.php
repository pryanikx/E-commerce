<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;

class RegisterRepository implements RegisterRepositoryInterface
{
    /**
     * register user
     *
     * @param array $credentials
     *
     * return array
     */
    public function register(array $credentials): array
    {
        $user = User::create($credentials);

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
