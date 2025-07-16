<?php

declare(strict_types=1);

namespace App\DTO\Email;

class EmailDTO
{
    /**
     * @param string $timestamp
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $content
     */
    public function __construct(
        public string $timestamp,
        public string $to,
        public string $from,
        public string $subject,
        public string $content,
    ) {
    }
}
