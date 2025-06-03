<?php

require_once __DIR__ . '/fetch.php';

$taxType = $_POST['tax-type'] ?? '';
$session = $_POST['session'] ?? '';
$jsSession = $_POST['js-session'] ?? '';
$user = $_POST['cd-user'] ?? '';
$ieEmit = $_POST['ie-emit'] ?? '';
$contribuitionType = $_POST['contribuition-type'] ?? '';
$dateStart = $_POST['date-start'] ?? '';
$dateEnd = $_POST['date-end'] ?? '';
$keysList = preg_split('/\R/', trim(str_replace("'", '', $_POST['keys-list']) ?? '')) ?? '';
$start = isset($_POST['start']) ? true : false;

$data = [
    'taxType' => $taxType,
    'session' => $session,
    'jsSession' => $jsSession,
    'user' => $user,
    'ieEmit' => $ieEmit,
    'contribuition-type' => $contribuitionType,
    'dateStart' => $dateStart,
    'dateEnd' => $dateEnd,
    'keysList' => $keysList,
    'start' => $start
];

$dataFinal = (int) new DateTime($data['dateEnd'])->format('d') - (int) new DateTime($data['dateStart'])->format('d') + 1;
$diaInicial = (int) new DateTime($data['dateStart'])->format('d');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baixar NFe e NFCE | e-Fisco PE</title>
    <!-- <link rel="shortcut icon" href="https://efiscoi.sefaz.pe.gov.br/favicon.ico" type="image/x-icon"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        h3 {
            margin: 0 auto;
            text-align: center;
            background-color: #f8a1a2;
            color: #f52b37;
            padding: 1rem;
            width: 50%;
            border-radius: 10px;
        }

        .container {
            display: grid;
            width: 70%;
        }

        #submit-button {
            background-color: #2b7fff;
            color: white;
        }

        #submit-button:hover {
            background-color: #51a2ff;
        }
    </style>
</head>

<body style="font-family: monospace;">
    <div style="display: flex; align-items: center; flex-direction: column;">
        <form method="post" class="container" style="border: 1px solid #ccc; border-radius: 10px; padding: 2rem;">
            <fieldset>
                <legend>Tipo de Nota:</legend>
                <div>
                    <input type="radio" id="nfe" name="tax-type" required value="nfe" <?= $taxType === 'nfe' ? 'checked' : '' ?> />
                    <label for="nfe">NFE (Nota Grande)</label>
                </div>
                <div>
                    <input type="radio" id="nfce" name="tax-type" required value="nfce" <?= $taxType === 'nfce' ? 'checked' : '' ?> />
                    <label for="nfce">NFCE (Cupom)</label>
                </div>
            </fieldset>
            <label for="session">ID Sessão</label>
            <input name="session" type="text" required value='<?= $_POST['session'] ?? '' ?>'>
            <label for="js-session">JS Sessão</label>
            <input name="js-session" type="text" required value='<?= $_POST['js-session'] ?? '' ?>'>
            <label for="cd-user">Cd. Usuário</label>
            <input name="cd-user" type="text" required value='<?= $_POST['cd-user'] ?? '' ?>'>
            <label for="ie-emit">I.E. Emitente</label>
            <input name="ie-emit" type="text" required value='<?= $_POST['ie-emit'] ?? '' ?>'>
            <fieldset>
                <legend>Tipo de contribuinte:</legend>
                <div>
                    <input type="radio" id="emitente" name="contribuition-type" value="E" <?= $contribuitionType === 'E' ? 'checked' : '' ?> />
                    <label for="emitente">Emitente (Saída)</label>
                </div>
                <div>
                    <input type="radio" id="destinatario" value="D" name="contribuition-type" <?= $contribuitionType === 'D' ? 'checked' : '' ?> />
                    <label for="destinatario">Destinatário (Entrada)</label>
                </div>
            </fieldset>
            <br>
            <div class="small-input">
                <label for="date-start">Data Inicial</label>
                <input name="date-start" type="date" value='<?= $_POST['date-start'] ?? '' ?>'>
                <label for="date-end">Data Final</label>
                <input name="date-end" type="date" value='<?= $_POST['date-end'] ?? '' ?>'>
                <br>
                <label for="keys-list">Chaves de Acesso</label>
                <textarea name="keys-list" pattern="(\d{44}\s*)+"><?=
                                                                    htmlspecialchars($_POST['keys-list'] ?? '', ENT_QUOTES)
                                                                    ?></textarea>
            </div>
            <br>
            <input type="submit" id="submit-button" name="start" value="Iniciar" class="small-input"></input>
        </form>
    </div>
    <br>
    <?php
    $tinyRequests = $data;
    $dataFim = str_replace('-', '/', new DateTime($data['dateEnd'])->format('d-m-Y'));

    $tipo = 'busca';
    if (isset($keysList[0]) && !empty($keysList[0])) {
        $tipo = 'chaves';
    }

    if ($start) {
        if ($tipo === 'busca') {
            for ($i = 0; $i < $dataFinal; $i++) {
                $d = $diaInicial;
                $d += $i;
                $dia = $d > 9 ? $d : '0' . $d;
                $parts = explode('/', $dataFim);
                $dtFim = "$parts[2]-$parts[1]-$dia";
                $tinyRequests['dateStart'] = $dtFim;
                $tinyRequests['dateEnd'] = $dtFim;
                fetch($tinyRequests);

                if ($i !== $dataFinal) {
                    sleep(rand(5, 15));
                }
            }
        } else {
            $tinyRequests['chavesDeAcesso'] = $keysList;
            download($tinyRequests);
        }
    }
    ?>
</body>

</html>