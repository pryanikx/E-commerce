<?php

declare(strict_types=1);

namespace App\DTO\Auth;

readonly class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->email = $request_data['email'];
        $this->password = $request_data['password'];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
