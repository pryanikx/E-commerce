<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class DeleteDataException extends Exception
{
    private const DELETION_FAILED = 'Failed to delete the data';

    /**
     * DeletionException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = self::DELETION_FAILED,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
