<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface LoginRepositoryInterface
{
    /**
     * login user
     *
     * @param array $credentials
     *
     * @return array
     */
    public function login(array $credentials): array;

    /**
     * logout user;
     *
     * @param User $user
     */
    public function logout(User $user): void;
}
