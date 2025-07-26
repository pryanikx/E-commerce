<?php

declare(strict_types=1);

namespace App\Services\Support;

use League\Csv\Writer;

interface CsvWriterFactoryInterface
{
    /**
     * Create a CSV Writer instance from a file path.
     *
     * @param string $path
     * @param string $mode
     *
     * @return Writer
     */
    public function createFromPath(string $path, string $mode): Writer;
}
