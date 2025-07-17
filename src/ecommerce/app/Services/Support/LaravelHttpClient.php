<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LaravelHttpClient implements HttpClientInterface
{
    /**
     * Http get query.
     *
     * @param string $url
     * @param array<string> $query
     *
     * @return Response
     * @throws ConnectionException
     */
    public function get(string $url, array $query = []): Response
    {
        return Http::get($url, $query);
    }
}
