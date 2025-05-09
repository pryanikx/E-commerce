<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Enums\UserRole;

readonly class RegisterDTO
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $email
     */
    public string $email;

    /**
     * @var string $password
     */
    public string $password;

    /**
     * @var string $role
     */
    public string $role;

    /**
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->email = $request_data['email'];
        $this->password = $request_data['password'];
        $this->role = UserRole::USER->value;
    }

    /**
     * @return array
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
