<?php
/**
 * User: Parvez
 * Date: 12/25/2017
 * Time: 4:39 AM
 */

namespace Csv;

class CsvFactory
{
    public static function fromString(string $csv): Csv
    {
        return new Csv($csv);
    }

    public static function fromArray(array $lines): Csv
    {
        return new Csv($lines);
    }

    public static function fromFile(string $filePath): Csv
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException("$filePath is not readable.");
        }
        return new Csv(file_get_contents($filePath));
    }
}