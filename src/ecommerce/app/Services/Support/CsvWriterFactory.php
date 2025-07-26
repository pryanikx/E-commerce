<?php

declare(strict_types=1);

namespace App\Services\Support;

use League\Csv\UnavailableStream;
use League\Csv\Writer;

class CsvWriterFactory implements CsvWriterFactoryInterface
{
    /**
     * Create a CSV Writer instance from a file path.
     *
     * @param string $path
     * @param string $mode
     *
     * @return Writer
     * @throws UnavailableStream
     */
    public function createFromPath(string $path, string $mode): Writer
    {
        return Writer::createFromPath($path, $mode);
    }
}
