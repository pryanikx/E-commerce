<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;

class RegisterService
{
    /**
     * @param RegisterRepositoryInterface $registerRepository
     */
    public function __construct(
        private RegisterRepositoryInterface $registerRepository,
        private Hasher $hasher,
    )
    {
    }

    /**
     * Register a new user.
     *
     * @param array $requestValidated
     *
     * @return array
     */
    public function register(array $requestValidated): array
    {
        $dto = $this->makeRegisterDTO($requestValidated);
        $data = $dto->toArray();
        $data['password'] = $this->hashPassword($data['password']);


        return $this->registerRepository->register($data);
    }

    /**
     * Create a new RegisterDTO.
     *
     * @param array $data
     *
     * @return RegisterDTO
     */
    private function makeRegisterDTO(array $data): RegisterDTO
    {
        return new RegisterDTO([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::USER->value,
        ]);
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
