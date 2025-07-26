<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\User\LoginDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class LoginService
{
    /**
     * @param AuthFactory $auth
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly AuthFactory $auth,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Login an existing user/admin.
     *
     * @param LoginDTO $dto
     *
     * @return UserDTO|array<string, null>
     * @throws \Exception
     */
    public function login(LoginDTO $dto): UserDTO|array
    {
        if (!$this->auth->guard()->attempt((array) $dto)) {
            return [
                'token' => null,
                'user' => null
            ];
        }

        $user = $this->auth->guard()->user();
        $token = $this->userRepository->createAccessToken($user);

        return $this->userRepository->mapToDTO($user, $token);
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
        $this->userRepository->deleteAccessToken($user);
    }
}
