<?php

namespace App\Utils;


/**
 * Converts a date string from 'DD/MM/YYYY' (Brazillian) format to 'YYYY-MM-DD' (ISO 8601 Standard) format.
 *
 * @param string $date The date string in 'DD/MM/YYYY' format to convert
 * @return string The converted date string in 'YYYY-MM-DD' format
 * @throws InvalidArgumentException If the input date doesn't match the expected format
 *
 * @example
 * $converted = date_convert('20/01/2025'); // returns '2025-01-20'
 */
function date_convert(string $date): string
{
    $parts = explode('/', $date);
    return "$parts[2]-$parts[1]-$parts[0]";
}
