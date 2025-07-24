<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;

class RegisterService
{
    /**
     * @param Hasher $hasher
     */
    public function __construct(
        private readonly Hasher $hasher,
    ) {
    }

    /**
     * Register a new user.
     *
     * @param array<string, string> $requestValidated
     *
     * @return array<string, mixed>
     */
    public function register(array $requestValidated): array
    {
        $user = User::create([
            'name' => $requestValidated['name'],
            'email' => $requestValidated['email'],
            'password' => $this->hashPassword($requestValidated['password']),
            'role' => UserRole::USER->value
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Hash the password.
     *
     * @param string $password
     *
     * @return string
     */
    private function hashPassword(string $password): string
    {
        return $this->hasher->make($password);
    }
}
