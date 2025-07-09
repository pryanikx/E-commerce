<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class LaravelHttpClient implements HttpClientInterface
{
    public function get(string $url, array $query = []): Response
    {
        return Http::get($url, $query);
    }
} 