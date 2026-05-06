<?php

namespace App\Scrapper;

/**
 * Encapsulates the result of parsing an invoice search page.
 */
class ScrapResult
{
    /**
     * @param array $keys List of invoice access keys found.
     * @param string $date The date associated with this search.
     * @param bool $isFull Whether the result reached the 500 records limit.
     * @param array $details List of metadata for each invoice (e.g., number, series).
     */
    public function __construct(
        public readonly array $keys,
        public readonly string $date,
        public readonly bool $isFull = false,
        public readonly array $details = []
    ) {}

    /**
     * @return int The total number of items found.
     */
    public function count(): int
    {
        return count($this->keys);
    }
}
