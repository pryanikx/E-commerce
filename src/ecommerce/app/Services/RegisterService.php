<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;

class RegisterService
{
    /**
     * Register a new user.
     *
     * @param array $request_validated
     *
     * @return array
     */
    public function register(array $request_validated): array
    {
        $dto = new RegisterDTO($request_validated);

        $user = User::create($dto->toArray());

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
