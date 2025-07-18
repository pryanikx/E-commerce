<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;

class RegisterService
{
    /**
     * @param RegisterRepositoryInterface $registerRepository
     * @param Hasher $hasher
     */
    public function __construct(
        private readonly RegisterRepositoryInterface $registerRepository,
        private readonly Hasher $hasher,
    ) {
    }

    /**
     * Register a new user.
     *
     * @param array<string, string> $requestValidated
     *
     * @return array<string, string>
     */
    public function register(array $requestValidated): array
    {
        $password = $this->hashPassword($requestValidated['password']);

        $userData = [
            'name' => $requestValidated['name'],
            'email' => $requestValidated['email'],
            'password' => $password,
            'role' => UserRole::USER->value,
        ];

        return $this->registerRepository->register($userData);
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
