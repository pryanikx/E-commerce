<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\LoginDTO;
use App\Models\User;
use App\Repositories\Contracts\LoginRepositoryInterface;

class LoginService
{

    public function __construct(
        private LoginRepositoryInterface $loginRepository,
    )
    {
    }

    /**
     * Login an existing user/admin.
     *
     * @param array $request_validated
     *
     * @return array
     * @throws \Exception
     */
    public function login(array $request_validated): array
    {
        $dto = $this->makeLoginDTO($request_validated);
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

    /**
     * Create a new LoginDTO.
     *
     * @param array $data
     * 
     * @return \App\DTO\Auth\LoginDTO
     */
    private function makeLoginDTO(array $data): \App\DTO\Auth\LoginDTO
    {
        return new \App\DTO\Auth\LoginDTO(
            $data['email'],
            $data['password']
        );
    }
}
