<?php

declare(strict_types=1);

namespace App\DTO\Auth;

readonly class LoginDTO
{
    public string $email;
    public string $password;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
