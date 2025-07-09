<?php

declare(strict_types=1);

namespace App\Services\Support;

use League\Csv\Writer;

class CsvWriterFactory implements CsvWriterFactoryInterface
{
    public function createFromPath(string $path, string $mode): Writer
    {
        return Writer::createFromPath($path, $mode);
    }
} 