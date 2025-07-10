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
    )
    {
    }

    /**
     * Login an existing user/admin.
     *
     * @param array $requestValidated
     *
     * @return array
     * @throws \Exception
     */
    public function login(array $requestValidated): array
    {
        $dto = new LoginDTO($requestValidated);
        $user_data = $this->loginRepository->login($dto->toArray());
        return [
            'token' => $user_data['token'],
            'user' => $user_data['user'],
        ];
    }

    /**
     * Logout user/admin
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
