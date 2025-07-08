<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

/**
 * Data transfer object for registration.
 */
readonly class RegisterDTO
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
    ) {}

    /**
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
