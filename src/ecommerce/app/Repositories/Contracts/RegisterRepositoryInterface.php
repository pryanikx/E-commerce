<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface RegisterRepositoryInterface
{
    /**
     * Register a user.
     *
     * @param array<string, string> $credentials
     *
     * @return array<string, mixed>
     */
    public function register(array $credentials): array;
}
