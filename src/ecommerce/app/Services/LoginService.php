<?php

namespace App\Services;

use App\Models\User;
use App\DTO\Auth\LoginDTO;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function login(LoginDTO $dto): array
    {
        $credentials = [$dto->email, $dto->password];

        if (!Auth::attempt($credentials)) {
            throw new \Exception("Invalid email or password");
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
