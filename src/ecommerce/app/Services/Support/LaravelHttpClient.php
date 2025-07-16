<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LaravelHttpClient implements HttpClientInterface
{
    public function get(string $url, array $query = []): Response
    {
        return Http::get($url, $query);
    }
}
