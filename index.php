<?php

require_once __DIR__ . '/App/Utils/dd.php';
require_once __DIR__ . '/App/Utils/pretty_print.php';
require_once __DIR__ . '/App/Utils/date_convert.php';
require_once __DIR__ . '/App/DTOs/SubmitDTO.php';
require_once __DIR__ . '/App/Http/Request.php';
require_once __DIR__ . '/App/Scrapper/Scrapper.php';

use function App\Utils\date_convert;
use function App\Utils\dd;
use function App\Utils\pretty_print;

use App\DTOs\FormSubmission\SubmitDTO;
use App\Http\Request;
use App\Scrapper\Scrapper;

$taxType = $_POST['tax-type'] ?? '';
$session = $_POST['session'] ?? '';
$jsSession = $_POST['js-session'] ?? '';
$user = $_POST['cd-user'] ?? '';
$ieEmit = $_POST['ie-emit'] ?? '';
$contribuitionType = $_POST['contribuition-type'] ?? '';
$dateStart = $_POST['date-start'] ?? '';
$dateEnd = $_POST['date-end'] ?? '';
$taxNumber = array_filter(
    preg_split('/\R/', trim(str_replace("'", '', $_POST['tax-number'] ?? ''))),
    fn($item) => $item !== ''
);
$taxSerie = $_POST['tax-serie'] ?? '';
$keysList = array_filter(
    preg_split('/\R/', trim(str_replace("'", '', $_POST['keys-list'] ?? ''))),
    fn($item) => $item !== ''
);

$start = isset($_POST['start']) ? true : false;

$data = [
    'taxType' => $taxType,
    'session' => trim($session),
    'jsSession' => trim($jsSession),
    'user' => trim($user),
    'ieEmit' => trim($ieEmit),
    'contribuition-type' => $contribuitionType,
    'dateStart' => $dateStart,
    'dateEnd' => $dateEnd,
    'taxNumber' => $taxNumber,
    'taxSerie' => $taxSerie,
    'keysList' => $keysList,
    'start' => $start
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baixar NFe e NFCE | e-Fisco PE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        *,
        *::after,
        *::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 0.9rem;
        }

        h3 {
            margin: 0 auto;
            text-align: center;
            background-color: #f52b37;
            color: white;
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
            background-color: mediumpurple;
        }

        #session-error {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body style="font-family: monospace;">
    <div style="display: flex; align-items: center; flex-direction: column;">
        <form method="post" class="container" style="border: 1px solid #ccc; box-shadow: 0 5px 10px 0 lightgray; border-radius: 10px; padding: 1rem;">
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
            <input name="session" type="text" required value='<?= $session ?? '' ?>'>
            <label for="js-session">JS Sessão</label>
            <input name="js-session" type="text" required value='<?= $jsSession ?? '' ?>'>
            <label for="cd-user">Código do Usuário</label>
            <input name="cd-user" type="text" required value='<?= $user ?? '' ?>'>
            <label for="ie-emit">I.E. Emitente</label>
            <input name="ie-emit" type="text" required value='<?= $ieEmit ?? '' ?>'>
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
                <input name="date-start" type="date" value='<?= $dateStart ?? '' ?>'>
                <label for="date-end">Data Final</label>
                <input name="date-end" type="date" value='<?= $dateEnd ?? '' ?>'>
                <br>
                <label for="tax-number">Número da Nota</label>
                <textarea name="tax-number" pattern="(\d{9}\s*)+"><?=
                                                                    htmlspecialchars($_POST['tax-number'] ?? '', ENT_QUOTES)
                                                                    ?></textarea>
                <label for="tax-number">Série</label>
                <input name="tax-serie" type="number" value='<?= $taxSerie ?? '' ?>'>
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
</body>

</html>

<?php
$dto = SubmitDTO::create($data);
$scrapper = new Scrapper($dto);
$request = new Request($dto);

$final = (int) new DateTime($dto->dateEnd)->format('d') - (int) new DateTime($dto->dateStart)->format('d') + 1;
$initial = (int) new DateTime($dto->dateStart)->format('d');
$dates = [];

if (!$start) {
    return;
}

$sum = [];

if (count($dto->keysList)) {
    $request->download($dto->keysList);
    $sum = $dto->keysList;

    show_success_message($sum);

    return;
}

if (count($dto->taxNumber)) {
    $keys = [];
    $date = new DateTime()->format('d/m/Y');

    foreach ($dto->taxNumber as $number) {
        $dto->taxNumber[0] = $number;
        $response = $request->NFCEAttempt($date);
        $scrapper->date = $date;
        $key = $scrapper->scrap($response);
        $sum = array_merge($sum, $key);
        $keys[] = $key[0];
        sleep(rand(5, 15));
    }

    $request->download($keys);
    show_success_message($sum);

    return;
}

// date format dd/mm/yyyy
for ($i = 0; $i < $final; $i++) {
    $d = $initial;
    $d += $i;
    $day = $d > 9 ? $d : '0' . $d;
    $parts = explode('-', $dto->dateEnd);
    $dt = "$day/$parts[1]/$parts[0]";

    $dates[] = $dt;
}

foreach ($dates as $date) {
    $keys = [];

    if ($dto->taxType === 'nfe') {
        $response = $request->NFEAttempt($date);
        $scrapper->date = $date;
        $keys = $scrapper->scrap($response);
    }

    if ($dto->taxType === 'nfce') {
        $response = $request->NFCEAttempt($date);
        $scrapper->date = $date;
        $keys = $scrapper->scrap($response);
    }

    if (count($keys) <= 0) {
        continue;
    }

    $request->date = date_convert($date);
    $request->download($keys);
    $sum = array_merge($sum, $keys);
    sleep(rand(5, 15));
}
show_success_message($sum);

function show_success_message(array $amount)
{
    echo '<br />';
    echo '<br />';
    echo '<p style="text-align: center; background-color: #00C951; color: white; padding: 1rem; border-radius: 10px;">' . 'Foram encontrados: '  . count($amount) . ' resultados!' . '</p>';
    echo '<br />';
}
