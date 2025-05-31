<?php
function fetchNfce(array $request)
{
    $taxType = $request['taxType'];
    $session = $request['session'];
    $jsSession = $request['jsSession'];
    $user = $request['user'];
    $ieEmitente = $request['ieEmit'];
    $tipoContribuinte = $request['contribuition-type'];
    $dataInicio = str_replace('-', '/', new DateTime($request['dateStart'])->format('d-m-Y'));
    $dataFim = str_replace('-', '/', new DateTime($request['dateEnd'])->format('d-m-Y'));

    $raw_data = http_build_query([
        'chamadaInterna' => 'true',
        'execCons' => 'true',
        'id_sessao' => $session,
        'dataIni' => $dataInicio,
        'dataFim' => $dataFim,
        'ieEmitente' => $ieEmitente,
        'cnpjEmitente' => '',
        'cpfCnpjDest' => '',
        'numNota' => '',
        'serie' => '',
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
        'Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=' . $session . "&cd_usuario=$user",
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

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $raw_data,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_COOKIE => 'JSESSIONID=' . $jsSession,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADER => false,
        CURLOPT_ENCODING => '',
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $encoding = mb_detect_encoding($response, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    $response = mb_convert_encoding($response, 'UTF-8', $encoding);

    return $response;
}
