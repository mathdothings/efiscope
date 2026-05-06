<?php

namespace App\Contracts;

use App\Scrapper\ScrapResult;

interface InvoiceProvider
{
    /**
     * Search for invoices on a specific date.
     *
     * @param string $date dd/mm/yyyy
     * @return ScrapResult
     */
    public function search(string $date): ScrapResult;

    /**
     * Download specific invoices as a ZIP.
     *
     * @param array $keys
     * @param string $date
     * @return string Path to the downloaded file.
     */
    public function download(array $keys, string $date): string;

    /**
     * Specialized download for individual keys.
     *
     * @param string $key
     * @return string Path to the downloaded file.
     */
    public function downloadByKey(string $key): string;
}
