<?php

require_once __DIR__ . '/download.php';
require_once __DIR__ . '/nfce_search.php';

function fetch(array $request)
{
    $taxType = $request['taxType'];
    $session = $request['session'];
    $jsSession = $request['jsSession'];
    $user = $request['user'];
    $ieEmitente = $request['ieEmit'];
    $tipoContribuinte = $request['contribuition-type'];
    $dataInicio = str_replace('-', '/', new DateTime($request['dateStart'])->format('d-m-Y'));
    $dataFim = str_replace('-', '/', new DateTime($request['dateEnd'])->format('d-m-Y'));

    $datas = [
        'dataIni' => $dataInicio,
        'dataFim' => $dataFim
    ];

    $datasQuery = http_build_query($datas);

    $url = $taxType === 'nfe' ? 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe' : 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce';

    $cookie = "JSESSIONID=$jsSession";

    $postData = $taxType === 'nfe'
        ? "chamadaInterna=true&execCons=true&id_sessao=$session&$datasQuery&tipoContrib=$tipoContribuinte&ieEmitente=$ieEmitente&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500"
        : "chamadaInterna=true&execCons=true&id_sessao=$session&$datasQuery&ieEmitente=$ieEmitente&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500";

    if ($taxType === 'nfe') {
        $ch = curl_init();

        $origin = $taxType === 'nfe'
            ? 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444'
            : 'Origin: https://nfce.sefaz.pe.gov.br:444';

        $refer = $taxType === 'nfe'
            ? "Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=$session&cd_usuario=$user"
            : "Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=$session&cd_usuario=$user";

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

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE => true,
        ]);

        $response = curl_exec($ch);

        $encoding = mb_detect_encoding($response, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        $response = mb_convert_encoding($response, 'UTF-8', $encoding);
    } else {
        $response = fetchNfce($request);
    }

    // var_dump($response);

    // if (curl_errno($ch)) {
    //     echo 'Error:' . curl_error($ch);
    // } else {
    //     // echo $response;
    // }

    $newDom = Dom\HTMLDocument::createFromString($response);
    $msgErro = $newDom->getElementById('msgErro');

    if (strpos($msgErro->innerHTML, 'ERRO')) {
        echo '<p style="background-color: #f8a1a2;
        color: #f52b37;
        padding: 1rem;
        width: 50%;
        border-radius: 10px;">Erro ao validar sessão!</p>';
        die();
    }

    $amount = $newDom->querySelectorAll('.thickbox');
    $elements = $newDom->querySelectorAll('.tabelaCadastroLinha');
    echo '<p>Total: ' . $amount->length . " ($dataInicio)" . '</p>';

    if ($amount->length <= 0) return;
    if ($amount->length === 500) echo '<h3 style="background-color: #f8a1a2; color: #f52b37; padding: 1rem;"> Houveram mais de 500 registros em ' . $dataInicio . '</h1>';

    $chavesDeAcesso = [];
    $chaves = [];

    echo '<details>';
    echo '<summary>Detalhes</summary>';
    echo '<table>';
    echo '    <caption>Informações</caption>';
    echo '    <thead>';
    echo '        <tr>';
    echo '            <th>Chave de Acesso</th>';
    echo '            <th style="width: 100px">Nota</th>';
    echo '            <th style="width: 100px">Série</th>';
    echo '        </tr>';
    echo '    </thead>';
    echo '    <tbody>';

    foreach ($elements as $element) {
        $chavesDeAcesso[] = $element->innerHTML;
    }

    if ($taxType === 'nfe') {
        $valores = array_chunk($chavesDeAcesso, 9);
    } else {
        $valores = array_chunk($chavesDeAcesso, 8);
    }

    foreach ($valores as $value) {
        echo '<tr>';
        $aTag = $value[1];

        if (preg_match('/<a[^>]*>([0-9]+)<\/a>/', $aTag, $matches)) {
            $number = $matches[1];
            echo '<td>' . $number . '</td>';
            $chaves[] = $number;
        }

        if ($taxType === 'nfe') {
            echo '<td>' . ($value[6]) . '</td>';
            echo '<td>' . ($value[7]) . '</td>';
            echo '</tr>';
        } else {
            echo '<td>' . ($value[5]) . '</td>';
            echo '<td>' . ($value[6]) . '</td>';
            echo '</tr>';
        }
    }

    echo '    </tbody>';
    echo '</table>';
    echo '</details>';

    $request['chavesDeAcesso'] = $chaves;

    if ($taxType === 'nfe') {
        curl_close($ch);
    }

    download($request);
}
