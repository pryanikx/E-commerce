<?php

namespace App\DTO\Auth;

readonly class LoginDTO
{
    public string $email;
    public string $password;

    public function __construct(array $request_data)
    {
        $this->email = $request_data['email'];
        $this->password = $request_data['password'];
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
