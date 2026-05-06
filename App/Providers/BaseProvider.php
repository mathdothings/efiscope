<?php

namespace App\Providers;

use App\Contracts\InvoiceProvider;
use App\DTOs\FormSubmission\SubmitDTO;
use App\Infrastructure\HttpClient;
use App\Scrapper\Scrapper;
use DateTime;
use DateTimeZone;

abstract class BaseProvider implements InvoiceProvider
{
    protected Scrapper $scrapper;

    public function __construct(
        protected readonly SubmitDTO $dto,
        protected readonly HttpClient $client
    ) {
        $this->scrapper = new Scrapper($this->dto->taxType);
    }

    /**
     * Common headers for SEFAZ requests.
     */
    protected function getHeaders(string $origin, string $referer): array
    {
        return [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Content-Type: application/x-www-form-urlencoded',
            "Origin: $origin",
            'DNT: 1',
            'Sec-GPC: 1',
            'Connection: keep-alive',
            "Referer: $referer",
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Priority: u=0, i',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0'
        ];
    }

    /**
     * Get the download path for a file, organized by IE and Tax Type.
     */
    protected function getDownloadPath(string $prefix, string $dateFormatted): string
    {
        $baseDir = realpath(__DIR__ . '/../../Output');

        // Use IE as base name or default to 'Generic'
        $ieFolder = preg_replace('/[^0-9]/', '', $this->dto->ieEmit) ?: 'Generic';

        // Use prefix (NFE, NFCE, ENTRADAS) as subfolder name
        $typeFolder = rtrim($prefix, '_');

        $finalDir = $baseDir . DIRECTORY_SEPARATOR . 'Downloaded' . DIRECTORY_SEPARATOR . $ieFolder . DIRECTORY_SEPARATOR . $typeFolder;

        if (!file_exists($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $time = new DateTime('now', new DateTimeZone('America/Sao_Paulo'))->format('Y-m-d_H-i-s');

        return $finalDir . DIRECTORY_SEPARATOR . $prefix . $dateFormatted . '_downloaded_' . $time . '.zip';
    }

    /**
     * Ensure the response is UTF-8 encoded.
     */
    protected function ensureUTF8(string $response): string
    {
        $encoding = mb_detect_encoding($response, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        return mb_convert_encoding($response, 'UTF-8', $encoding ?: 'ISO-8859-1');
    }
}
