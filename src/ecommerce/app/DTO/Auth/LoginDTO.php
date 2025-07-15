<?php

declare(strict_types=1);

namespace App\DTO\Auth;

readonly class LoginDTO
{
    public string $email;
    public string $password;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
