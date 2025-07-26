<?php

declare(strict_types=1);

namespace App\Exceptions\Currency;

use Exception;

class CurrencyApiException extends Exception
{
    private const DEFAULT_ERROR_MESSAGE = 'Currency API error';

    public function __construct(
        string $message = self::DEFAULT_ERROR_MESSAGE,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Check if this is a temporary error that might resolve with retry.
     *
     * @return bool
     */
    public function isTemporary(): bool
    {
        return $this->code >= 500 && $this->code < 600;
    }

    /**
     * Check if this is a client error (4xx).
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }
}
