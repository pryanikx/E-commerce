<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;

class RegisterService
{

    public function __construct(
        protected RegisterRepositoryInterface $registerRepository,
    )
    {
    }

    /**
     * Register a new user.
     *
     * @param array $request_validated
     *
     * @return array
     */
    public function register(array $request_validated): array
    {
        $dto = new RegisterDTO($request_validated);

        $created_user = $this->registerRepository->register($dto->toArray());

        return [
            'user' => $created_user['user'],
            'token' => $created_user['token'],
        ];
    }
}
