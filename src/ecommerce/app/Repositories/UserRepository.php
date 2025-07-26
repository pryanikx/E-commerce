<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\User\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user with an authentication token.
     *
     * @param RegisterDTO $dto
     * @return UserDTO
     */
    public function create(RegisterDTO $dto): UserDTO
    {
        $user = User::create($dto->toArray());
        $token = $this->createAccessToken($user);

        return $this->mapToDTO($user, $token);
    }

    /**
     * Create access token for user.
     *
     * @param User $user
     *
     * @return string
     */
    public function createAccessToken(User $user): string
    {
        return $user->createToken('token')->plainTextToken;
    }

    /**
     * Delete user's access token from the database.
     *
     * @param User $user
     *
     * @return void
     */
    public function deleteAccessToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return UserDTO
     */
    public function find(int $id): UserDTO
    {
        return $this->mapToDTO(User::findOrFail($id));
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return (bool) User::destroy($id);
    }

    /**
     * Map Eloquent model to DTO.
     *
     * @param User $user
     * @param string|null $token
     * @return UserDTO
     */
    public function mapToDTO(User $user, ?string $token = null): UserDTO
    {
        return new UserDTO(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
            token: $token,
        );
    }
}
