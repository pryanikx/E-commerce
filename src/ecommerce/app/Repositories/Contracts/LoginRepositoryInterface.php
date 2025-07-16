<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface LoginRepositoryInterface
{
    /**
     * Login a user.
     *
     * @param array<string, string> $credentials
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function login(array $credentials): array;

    /**
     * Logout a user.
     *
     * @param User $user
     */
    public function logout(User $user): void;
}
