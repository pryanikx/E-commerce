<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\User\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user with an authentication token.
     *
     * @param RegisterDTO $dto
     * @return UserDTO
     */
    public function create(RegisterDTO $dto): UserDTO;

    /**
     * Create access token for user.
     *
     * @param User $user
     *
     * @return string
     */
    public function createAccessToken(User $user): string;

    /**
     * Delete user's access token from the database.
     *
     * @param User $user
     *
     * @return void
     */
    public function deleteAccessToken(User $user): void;

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return UserDTO
     */
    public function find(int $id): UserDTO;

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Map Eloquent model to DTO.
     *
     * @param User $user
     * @param string|null $token
     * @return UserDTO
     */
    public function mapToDTO(User $user, ?string $token = null): UserDTO;
}
