<?php

declare(strict_types=1);

namespace App\DTO\User;

class UserDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $role
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
    ) {
    }
}
