<?php

declare(strict_types=1);

namespace App\Application\Utils;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ArrayUtils
{
    public static function isAssocArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function getFirstStringKeyInAssocArray(array $array): ?string
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return $key;
            }
        }

        return null;
    }

    public static function flatten(array $array, bool $useKeys = true): array
    {
        return iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)), $useKeys);
    }
}
