<?php

namespace App\Providers;

use App\Scrapper\ScrapResult;

class NfceProvider extends BaseProvider
{
    private const BASE_URL = 'https://nfce.sefaz.pe.gov.br:444/nfce-web';

    public function search(string $date): ScrapResult
    {
        $url = self::BASE_URL . '/downloadNfce';
        $referer = "$url?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao={$this->dto->session}&cd_usuario={$this->dto->user}";
        $headers = $this->getHeaders('https://nfce.sefaz.pe.gov.br:444', $referer);

        $postData = [
            'chamadaInterna' => 'true',
            'execCons' => 'true',
            'id_sessao' => $this->dto->session,
            'dataIni' => $date,
            'dataFim' => $date,
            'cd_usuario' => $this->dto->user,
            'ieEmitente' => $this->dto->ieEmit,
            'cnpjEmitente' => '',
            'cpfCnpjDest' => '',
            'numNota' => $this->dto->taxNumber[0] ?? '',
            'serie' => $this->dto->taxSerie,
            'chave' => '',
            'prot' => '',
            'pages' => '500'
        ];

        $post = http_build_query($postData);

        $response = $this->client->post($url, $post, $headers, $this->dto->jsSession);

        $this->scrapper->date = $date;
        return $this->scrapper->scrap($this->ensureUTF8($response));
    }

    public function download(array $keys, string $dateFormatted): string
    {
        $url = self::BASE_URL . '/downloadNota';
        $referer = self::BASE_URL . '/downloadNfce';
        $headers = $this->getHeaders('https://nfce.sefaz.pe.gov.br:444', $referer);

        $postData = [
            'chamadaInterna' => 'true',
            'execCons' => '',
            'id_sessao' => $this->dto->session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $this->formatDate($this->dto->dateStart),
            'dataFim' => $this->formatDate($this->dto->dateEnd),
            'ieEmitente' => $this->dto->ieEmit,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => '',
            'prot' => '',
            'pages' => '500',
            'lista' => 'on',
            'cb' => $keys,
        ];

        $postFields = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
        $postFields = preg_replace('/%5B\d+%5D=/', '=', $postFields);

        $prefix = count($this->dto->keysList) ? 'ENTRADAS_' : 'NFCE_';
        $filepath = $this->getDownloadPath($prefix, $dateFormatted);

        $this->client->downloadToFile($url, $filepath, $postFields, $headers, $this->dto->jsSession);

        return $filepath;
    }

    public function downloadByKey(string $key): string
    {
        $searchUrl = self::BASE_URL . '/downloadNfce';
        $downloadUrl = self::BASE_URL . '/downloadNota';
        $referer = self::BASE_URL . '/downloadNfce';
        $headers = $this->getHeaders('https://nfce.sefaz.pe.gov.br:444', $referer);

        $dataIni = $this->formatDate($this->dto->dateStart);
        $dataFim = $this->formatDate($this->dto->dateEnd);

        // STEP 1: Search Request (to select the key in the session)
        $searchData = [
            'chamadaInterna' => 'true',
            'execCons' => 'true',
            'id_sessao' => $this->dto->session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataIni,
            'dataFim' => $dataFim,
            'ieEmitente' => $this->dto->ieEmit,
            'cnpjEmitente' => '',
            'cpfCnpjDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => $key,
            'prot' => '',
            'pages' => '25'
        ];

        $searchFields = http_build_query($searchData, '', '&', PHP_QUERY_RFC3986);
        $searchFields = str_replace('%5E', '^', $searchFields);
        $this->client->post($searchUrl, $searchFields, $headers, $this->dto->jsSession);

        // Small wait as in the original code
        sleep(2);

        // STEP 2: Download Request (to fetch the ZIP)
        $downloadData = [
            'chamadaInterna' => 'true',
            'execCons' => '',
            'id_sessao' => $this->dto->session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataIni,
            'dataFim' => $dataFim,
            'ieEmitente' => $this->dto->ieEmit,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => $key,
            'prot' => '',
            'pages' => '25',
            'lista' => 'on',
            'cb' => $key
        ];

        $downloadFields = http_build_query($downloadData, '', '&', PHP_QUERY_RFC3986);
        $downloadFields = preg_replace('/%5B\d+%5D=/', '=', $downloadFields);
        $downloadFields = str_replace('%5E', '^', $downloadFields);

        $filepath = $this->getDownloadPath('ENTRADAS_', $key);

        $this->client->downloadToFile($downloadUrl, $filepath, $downloadFields, $headers, $this->dto->jsSession);

        return $filepath;
    }

    private function formatDate(string $date): string
    {
        if (empty($date)) {
            return date('d/m/Y');
        }
        return (new \DateTime($date))->format('d/m/Y');
    }
}
