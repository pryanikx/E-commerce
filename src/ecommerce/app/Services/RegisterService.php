<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\User\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;

class RegisterService
{
    /**
     * @param UserRepositoryInterface $userRepository
     * @param Hasher $hasher
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private Hasher $hasher,
    ) {
    }

    /**
     * Register a new user.
     *
     * @param RegisterDTO $dto
     *
     * @return UserDTO
     */
    public function register(RegisterDTO $dto): UserDTO
    {
        $dto->password = $this->hashPassword($dto->password);

        return $this->userRepository->create($dto);
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
