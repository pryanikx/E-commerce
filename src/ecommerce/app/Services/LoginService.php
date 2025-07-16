<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\LoginDTO;
use App\Models\User;
use App\Repositories\Contracts\LoginRepositoryInterface;

class LoginService
{
    public function __construct(
        private readonly LoginRepositoryInterface $loginRepository,
    ) {
    }

    /**
     * Login an existing user/admin.
     *
     * @param array<string, string> $requestValidated
     *
     * @return array<string, string>
     * @throws \Exception
     */
    public function login(array $requestValidated): array
    {
        $dto = new LoginDTO(
            email: $requestValidated['email'],
            password: $requestValidated['password'],
        );

        return $this->loginRepository->login((array) $dto);
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
        $this->loginRepository->logout($user);
    }
}
