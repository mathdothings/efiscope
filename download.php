<?php

function download(array $request)
{
    $taxType = $request['taxType'];
    $session = $request['session'];
    $jsSession = $request['jsSession'];
    $user = $request['user'];
    $ieEmitente = $request['ieEmit'];
    $tipoContribuinte = $request['contribuition-type'];
    $dataInicio = str_replace('-', '/', new DateTime($request['dateStart'] ?? new DateTime())->format('d-m-Y'));
    $dataFim = str_replace('-', '/', new DateTime($request['dateEnd'] ?? new DateTime())->format('d-m-Y'));
    $chaves = $request['chavesDeAcesso'];
    $ch = curl_init();

    $url = $taxType === 'nfe' ? 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNota' : 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNota';
    curl_setopt($ch, CURLOPT_URL, $url);

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_COOKIE, "JSESSIONID=$jsSession");

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

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $downloadDir = __DIR__ . DIRECTORY_SEPARATOR . 'downloads';
    if (!file_exists($downloadDir)) {
        mkdir($downloadDir, 0755, true);
    }

    $prefix = $taxType === 'nfe' ? 'NFE_' : 'NFCE_';

    $outputFile = $downloadDir
        . DIRECTORY_SEPARATOR
        . $prefix
        . new DateTime($request['dateEnd'])->format('d-m-Y_H-i-s')
        . uniqid()
        . '.zip';

    $fileHandle = fopen($outputFile, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fileHandle);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        fclose($fileHandle);
        unlink($outputFile);
    } else {
        fclose($fileHandle);

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        if (strpos($contentType, 'application/zip') !== false) {
            echo "Os arquivos foram baixados em: $outputFile";
        } else {
            $content = file_get_contents($outputFile);
            unlink($outputFile);
            echo "Response:\n" . htmlspecialchars($content);
        }
    }

    curl_close($ch);
}
