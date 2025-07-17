<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    /**
     * Http get query.
     *
     * @param string $url
     * @param array<string> $query
     *
     * @return Response
     */
    public function get(string $url, array $query = []): Response;
}
