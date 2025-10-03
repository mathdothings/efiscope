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
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
            'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Linux"'
        ];

        $post = "chamadaInterna=true&execCons=true&id_sessao=$session&$datasQuery&tipoContrib=$tipoContribuinte&ieEmitente=$ieEmitente&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500";

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
        curl_close($curlHandler);

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
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
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
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => '',
        ]);

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

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
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
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

        $downloadDir = __DIR__ . '/../../Output';

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
            $content = file_get_contents($filepath);
            unlink($filepath);
        }

        curl_close($curlHandler);
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
