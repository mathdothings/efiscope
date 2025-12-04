<?php

namespace App\Http;

use DateTime;

use DateTimeZone;
use App\DTOs\FormSubmission\SubmitDTO;

final class Request
{
    public string $date = '';
    public function __construct(private SubmitDTO $dto) {}

    function NFEAttempt(string $date): string
    {
        $curlHandler = curl_init();

        $url = 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe';

        $session = $this->dto->session;
        $user = $this->dto->user;
        $datasQuery =  http_build_query([
            'dataIni' => $date,
            'dataFim' => $date
        ]);
        $tipoContribuinte = $this->dto->contribuitionType;
        $ieEmitente = $this->dto->ieEmit;
        $cookie = $this->dto->jsSession;

        $origin = 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444';
        $refer = "Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=$session&cd_usuario=$user";
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            $origin,
            'Pragma: no-cache',
            $refer,
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
            'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"'
        ];

        $post = "chamadaInterna=true&execCons=true&id_sessao=$session&$datasQuery&tipoContrib=$tipoContribuinte&ieEmitente=$ieEmitente&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500&cd_usuario={$this->dto->user}";

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE => true,
        ]);

        $response = curl_exec($curlHandler);
        unset($curlHandler);

        return $this->ensureUTF8Enconding($response);
    }

    // dd/mm/yyyy
    public function NFCEAttempt(string $date)
    {
        $curlHandler = curl_init();

        $raw_data = http_build_query([
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
        ]);

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://nfce.sefaz.pe.gov.br:444',
            'Pragma: no-cache',
            'Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=' . $this->dto->session . "&cd_usuario=" . $this->dto->user,
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
            'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"'
        ];

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $raw_data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $this->dto->jsSession,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => '',
        ]);

        $response = curl_exec($curlHandler);
        unset($curlHandler);

        return $this->ensureUTF8Enconding($response);
    }

    public function download(array $keys)
    {
        $taxType = $this->dto->taxType;
        $session = $this->dto->session;
        $jsSession = $this->dto->jsSession;
        $ieEmitente = $this->dto->ieEmit;
        $tipoContribuinte = $this->dto->contribuitionType;
        $dataInicio = str_replace('-', '/', new DateTime($this->dto->dateStart)->format('d-m-Y'));
        $dataFim = str_replace('-', '/', new DateTime($this->dto->dateEnd)->format('d-m-Y'));
        $chaves = $keys;

        $curlHandler = curl_init();

        $url = $taxType === 'nfe'
            ? 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNota'
            : 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNota';

        curl_setopt($curlHandler, CURLOPT_URL, $url);

        $origin = $taxType === 'nfe'
            ? 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444'
            : 'Origin: https://nfce.sefaz.pe.gov.br:444';

        $refer = $taxType === 'nfe'
            ? "Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe"
            : "Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce";

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            $origin,
            'Pragma: no-cache',
            $refer,
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36',
            'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"'
        ];

        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandler, CURLOPT_COOKIE, $jsSession);

        $postData = $taxType === 'nfe' ? [
            'chamadaInterna' => 'true',
            'execCons' => '',
            'id_sessao' => $session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataInicio,
            'dataFim' => $dataFim,
            'tipoContrib' => $tipoContribuinte,
            'ieEmitente' => $ieEmitente,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => '',
            'prot' => '',
            'pages' => '500',
            'lista' => 'on',
            'cb' => $chaves
        ] : [
            'chamadaInterna' => 'true',
            'execCons' => '',
            'id_sessao' => $session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataInicio,
            'dataFim' => $dataFim,
            'ieEmitente' => $ieEmitente,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => '',
            'prot' => '',
            'pages' => '500',
            'lista' => 'on',
            'cb' => $chaves
        ];

        $postFields = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
        $postFields = preg_replace('/%5B\d+%5D=/', '=', $postFields);

        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postFields);

        $downloadDir = realpath(__DIR__ . '/../../Output');

        if (!file_exists($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        $prefix = $taxType === 'nfe' ? 'NFE_' : 'NFCE_';

        if (count($this->dto->keysList)) {
            $prefix = 'CHAVES_';
        }

        $filepath = $downloadDir
            . DIRECTORY_SEPARATOR
            . $prefix
            . new DateTime($this->date, timezone: new DateTimeZone('America/Sao_Paulo'))->format('d-m-Y')
            . '_downloaded_'
            . new DateTime(timezone: new DateTimeZone('America/Sao_Paulo'))->format('d-m-Y_H-i-s')
            . '.zip';

        $fileHandle = fopen($filepath, 'w');
        curl_setopt($curlHandler, CURLOPT_FILE, $fileHandle);

        curl_exec($curlHandler);

        if (curl_errno($curlHandler)) {
            echo 'Error:' . curl_error($curlHandler);
            fclose($fileHandle);
            unlink($filepath);
        }

        fclose($fileHandle);
        $contentType = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);

        // 22 bytes represents an empty minimal .zip file
        if (strpos($contentType, 'application/zip') !== false && filesize($filepath) > 22) {
            echo '<p style="margin: 5px 0; text-align: center; background-color: #F0FDF4; color: #00C951; padding: 0.5rem 1rem; border: 1px solid #B9F8CF; border-radius: 6px;">' . 'Os arquivos foram baixados em: ' . realpath($filepath) . '</p>';
        }

        if (strpos($contentType, 'application/zip') === false) {
            unlink($filepath);
        }

        unset($curlHandler);
    }

    public function downloadByKey(array $keys)
    {
        $taxType = $this->dto->taxType;
        $session = $this->dto->session;
        $jsSession = $this->dto->jsSession;
        $ieEmitente = $this->dto->ieEmit;
        $tipoContribuinte = $this->dto->contribuitionType;
        $dataInicio = str_replace('-', '/', new DateTime($this->dto->dateStart)->format('d-m-Y'));
        $dataFim = str_replace('-', '/', new DateTime($this->dto->dateEnd)->format('d-m-Y'));
        $chaves = $keys;

        $downloadDir = realpath(__DIR__ . '/../../Output');

        if (!file_exists($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        $downloadedCount = 0;
        $failedCount = 0;

        foreach ($chaves as $index => $key) {
            echo "<p>Baixando chave " . ($index + 1) . " de " . count($chaves) . " Chave: {$key}</p>";

            $searchResult = $this->makeSearchRequest($key, $session, $jsSession, $dataInicio, $dataFim, $tipoContribuinte, $ieEmitente);

            if (!$searchResult) {
                echo '<p style="color: orange;">Falha na consulta inicial para chave: ' . $key . '</p>';
                $failedCount++;
                continue;
            }

            // waiting time between search and download
            sleep(2);

            $downloadResult = $this->makeDownloadRequest($key, $session, $jsSession, $dataInicio, $dataFim, $tipoContribuinte, $ieEmitente);

            if ($downloadResult && is_array($downloadResult)) {
                list($response, $httpCode, $contentType) = $downloadResult;

                if ($httpCode === 200 && strpos($contentType, 'application/zip') !== false && strlen($response) > 22) {
                    $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
                    $filename = "CHAVE_{$safeKey}_" . date('Y-m-d_H-i-s') . '.zip';
                    $filepath = $downloadDir . DIRECTORY_SEPARATOR . $filename;

                    file_put_contents($filepath, $response);

                    if (filesize($filepath) > 22) {
                        echo '<p style="margin: 5px 0; text-align: center; background-color: #F0FDF4; color: #00C951; padding: 0.5rem 1rem; border: 1px solid #B9F8CF; border-radius: 6px;">' .
                            '✓ Baixado: ' . basename($filepath) . ' (' . filesize($filepath) . ' bytes)' .
                            '</p>';
                        $downloadedCount++;
                    } else {
                        echo '<p style="color: orange;">Arquivo ZIP vazio para chave: ' . $key . '</p>';
                        unlink($filepath);
                        $failedCount++;
                    }
                } else {
                    if (strpos($response, 'Não foram encontrados itens no banco de dados para a consulta.') !== false) {
                        echo '<p style="color: red;">✗ A chave não foi encontrada na base.' . $key . '. HTTP: ' . $httpCode . ', Tipo: ' . $contentType . '</p>';
                    } else {
                        echo '<p style="color: red;">✗ Falha ao baixar chave ' . $key . '. HTTP: ' . $httpCode . ', Tipo: ' . $contentType . '</p>';
                        $failedCount++;
                    }
                }
            } else {
                echo '<p style="color: red;">✗ Erro na requisição de download para chave: ' . $key . '</p>';
                $failedCount++;
            }

            if ($index < count($chaves) - 1) {
                // waiting time after download, avoid too many requests
                sleep(3);
            }
        }
    }

    private function makeSearchRequest($key, $session, $jsSession, $dataInicio, $dataFim, $tipoContribuinte, $ieEmitente)
    {
        $curlHandler = curl_init();

        $url = 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe';
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 30);

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://nfeconsulta.sefaz.pe.gov.br:444',
            'DNT: 1',
            'Sec-GPC: 1',
            'Connection: keep-alive',
            'Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Priority: u=0, i',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0'
        ];

        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandler, CURLOPT_COOKIE, $jsSession);
        curl_setopt($curlHandler, CURLOPT_ENCODING, 'gzip, deflate, br, zstd');

        $postData = [
            'chamadaInterna' => 'true',
            'execCons' => 'true',
            'id_sessao' => $session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataInicio,
            'dataFim' => $dataFim,
            'tipoContrib' => $tipoContribuinte,
            'ieEmitente' => $ieEmitente,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => $key,
            'prot' => '',
            'pages' => '25'
        ];

        $postFields = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
        $postFields = str_replace('%5E', '^', $postFields);

        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postFields);

        curl_exec($curlHandler);
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        unset($curlHandler);

        return $httpCode === 200;
    }

    private function makeDownloadRequest($key, $session, $jsSession, $dataInicio, $dataFim, $tipoContribuinte, $ieEmitente)
    {
        $curlHandler = curl_init();

        $url = 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNota';
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 30);

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://nfeconsulta.sefaz.pe.gov.br:444',
            'DNT: 1',
            'Sec-GPC: 1',
            'Connection: keep-alive',
            'Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Priority: u=0, i',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0'
        ];

        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandler, CURLOPT_COOKIE, $jsSession);
        curl_setopt($curlHandler, CURLOPT_ENCODING, 'gzip, deflate, br, zstd');

        $postData = [
            'chamadaInterna' => 'true',
            'execCons' => '',
            'id_sessao' => $session,
            'cd_usuario' => $this->dto->user,
            'dataIni' => $dataInicio,
            'dataFim' => $dataFim,
            'tipoContrib' => $tipoContribuinte,
            'ieEmitente' => $ieEmitente,
            'cpfCnpjEmitDest' => '',
            'numNota' => '',
            'serie' => '',
            'chave' => $key,
            'prot' => '',
            'pages' => '25',
            'lista' => 'on',
            'cb' => $key
        ];

        $postFields = http_build_query($postData, '', '&', PHP_QUERY_RFC3986);
        $postFields = preg_replace('/%5B\d+%5D=/', '=', $postFields);
        $postFields = str_replace('%5E', '^', $postFields);

        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($curlHandler);

        if (curl_errno($curlHandler)) {
            unset($curlHandler);
            return false;
        }

        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);

        unset($curlHandler);

        return [$response, $httpCode, $contentType];
    }

    /**
     * Ensures the response is UTF-8 encoded.
     *
     * @param string $response The response to convert
     * @return string UTF-8 encoded response
     */
    private function ensureUTF8Enconding(string $response): string
    {
        $encoding = mb_detect_encoding($response, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        return mb_convert_encoding($response, 'UTF-8', $encoding);
    }
}
