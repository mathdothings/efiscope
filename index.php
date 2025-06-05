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
$keysList = array_filter(
    preg_split('/\R/', trim(str_replace("'", '', $_POST['keys-list'] ?? ''))),
    fn($item) => $item !== ''
);

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baixar NFe e NFCE | e-Fisco PE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        body {
            font-size: 1rem;
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
            background-color: #51a2ff;
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

if (count($dto->keysList)) {
    $request->download($dto->keysList);
}

// data format dd/mm/yyyy
for ($i = 0; $i < $final; $i++) {
    $d = $initial;
    $d += $i;
    $day = $d > 9 ? $d : '0' . $d;
    $parts = explode('-', $dto->dateEnd);
    $dt = "$day/$parts[1]/$parts[0]";

    $dates[] = $dt;
}

$sum = [];
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

echo '<br />';
echo '<br />';
echo '<h3 style="background-color: #2B7FFF; color: white; padding: 1rem;">' . 'A operação finalizou, foram encontrados: ' . '<span style="font-size: 1.15rem; color: #00C951;">' . count($sum) . '</span>' . ' resultados' . '</h3>';
echo '<br />';
