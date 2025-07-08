<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\Contracts\RegisterRepositoryInterface;
use Illuminate\Support\Facades\Hash;

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
        $dto = $this->makeRegisterDTO($request_validated);
        $data = $dto->toArray();
        $data['password'] = $this->hashPassword($data['password']);
        $created_user = $this->registerRepository->register($data);
        return [
            'user' => $created_user['user'],
            'token' => $created_user['token'],
        ];
    }

    /**
     * Создаёт RegisterDTO из массива данных
     *
     * @param array $data
     * @return \App\DTO\Auth\RegisterDTO
     */
    private function makeRegisterDTO(array $data): \App\DTO\Auth\RegisterDTO
    {
        return new \App\DTO\Auth\RegisterDTO(
            $data['name'],
            $data['email'],
            $data['password'],
            \App\Enums\UserRole::USER->value
        );
    }

    /**
     * Hash the password.
     *
     * @param string $password
     * @return string
     */
    private function hashPassword(string $password): string
    {
        return \Illuminate\Support\Facades\Hash::make($password);
    }
}
