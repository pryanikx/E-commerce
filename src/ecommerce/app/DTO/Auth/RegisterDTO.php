<?php

declare(strict_types=1);

namespace App\DTO\Auth;

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
        public string $role
    )
    {
    }
}
