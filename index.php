<?php

require_once __DIR__ . '/App/Utils/dd.php';
require_once __DIR__ . '/App/Utils/pretty_print.php';
require_once __DIR__ . '/App/Utils/date_convert.php';
require_once __DIR__ . '/App/Utils/unzip.php';
require_once __DIR__ . '/App/Utils/delete_all_files.php';

require_once __DIR__ . '/App/DTOs/SubmitDTO.php';
require_once __DIR__ . '/App/Http/Request.php';
require_once __DIR__ . '/App/Scrapper/Scrapper.php';

use App\Http\Request;
use App\Scrapper\Scrapper;
use App\DTOs\FormSubmission\SubmitDTO;

use function App\Utils\dd;
use function App\Utils\date_convert;
use function App\Utils\delete_all_files;
use function App\Utils\pretty_print;
use function App\Utils\unzip;

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

require_once __DIR__ . '/frontend.php';

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
    unzip();
    delete_all_files();
    die;
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
    unzip();
    delete_all_files();
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
unzip();
delete_all_files();

function show_success_message(array $amount)
{
    echo '<br />';
    echo '<br />';
    echo '<p style="text-align: center; background-color: #00C951; color: white; padding: 1rem; border-radius: 10px;">' . 'Foram encontrados: '  . count($amount) . ' resultados!' . '</p>';
    echo '<br />';
}
