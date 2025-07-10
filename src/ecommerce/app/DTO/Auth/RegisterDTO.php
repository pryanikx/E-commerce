<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

readonly class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->role = $data['role'];
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
