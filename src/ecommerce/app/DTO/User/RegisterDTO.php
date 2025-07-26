<?php

declare(strict_types=1);

namespace App\DTO\User;

class RegisterDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ) {
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
