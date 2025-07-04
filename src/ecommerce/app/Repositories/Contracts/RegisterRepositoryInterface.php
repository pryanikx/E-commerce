<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface RegisterRepositoryInterface
{
    /**
     * register user
     *
     * @param array $credentials
     *
     * @return array
     */
    public function register(array $credentials): array;
}
