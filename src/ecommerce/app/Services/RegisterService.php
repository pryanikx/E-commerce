<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    /**
     * Register a new user.
     *
     * @param RegisterDTO $dto
     *
     * @return array
     */
    public function register(RegisterDTO $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
