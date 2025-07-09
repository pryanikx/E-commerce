<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    public function get(string $url, array $query = []): Response;
} 