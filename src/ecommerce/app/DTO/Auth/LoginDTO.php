<?php

declare(strict_types=1);

namespace App\DTO\Auth;

/**
 * Data transfer object for login.
 */
readonly class LoginDTO
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
