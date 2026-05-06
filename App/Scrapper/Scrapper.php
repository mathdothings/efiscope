<?php

namespace App\Scrapper;

use Dom\HTMLDocument;
use App\Scrapper\ScrapResult;

final class Scrapper
{
    /**
     * @param string $taxType 'nfe' | 'nfce'
     * @param string $date
     */
    public function __construct(
        private string $taxType,
        public string $date = ''
    ) {}

    /**
     * Parse the HTML response and return a ScrapResult.
     *
     * @param string $response
     * @return ScrapResult
     * @throws \RuntimeException If the session is invalid.
     */
    public function scrap(string $response): ScrapResult
    {
        $dom = HTMLDocument::createFromString($response);
        $error = $dom->getElementById('msgErro');

        if ($error && str_contains($error->innerHTML, 'ERRO')) {
            throw new \RuntimeException("Invalid session detected");
        }

        $amountElements = $dom->querySelectorAll('.thickbox');
        $dataElements = $dom->querySelectorAll('.tabelaCadastroLinha');
        $isFull = $amountElements->length >= 500;

        if ($amountElements->length === 0) {
            return new ScrapResult([], $this->date, false, []);
        }

        $chavesDeAcesso = [];
        foreach ($dataElements as $element) {
            $chavesDeAcesso[] = $element->innerHTML;
        }

        $chunkSize = ($this->taxType === 'nfe') ? 9 : 8;
        $valores = array_chunk($chavesDeAcesso, $chunkSize);

        $keys = [];
        $details = [];

        foreach ($valores as $value) {
            $aTag = $value[1];
            $key = '';

            if (preg_match('/<a[^>]*>([0-9]+)<\/a>/', $aTag, $matches)) {
                $key = $matches[1];
                $keys[] = $key;
            }

            if ($this->taxType === 'nfe') {
                $details[] = [
                    'key' => $key,
                    'number' => $value[6] ?? '',
                    'serie' => $value[7] ?? '',
                ];
            } else {
                $details[] = [
                    'key' => $key,
                    'number' => $value[5] ?? '',
                    'serie' => $value[6] ?? '',
                ];
            }
        }

        return new ScrapResult($keys, $this->date, $isFull, $details);
    }
}
