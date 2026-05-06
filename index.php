<?php

session_start();
require_once __DIR__ . '/autoload.php';

use App\DTOs\FormSubmission\SubmitDTO;
use App\Infrastructure\HttpClient;
use App\Providers\NfceProvider;
use App\Providers\NfeProvider;
use App\Services\DownloaderService;
use App\Services\UIResponder;

// Helper to get value from POST or SESSION
$getVal = fn($key, $default = '') => $_POST[$key] ?? $_SESSION[$key] ?? $default;

// Basic input group
$taxType = $getVal('tax-type', 'nfe');
$session = $getVal('session');
$jsSession = $getVal('js-session');
$user = $getVal('cd-user');
$ieEmit = $getVal('ie-emit');
$contribuitionType = $getVal('contribuition-type');
$dateStart = $getVal('date-start');
$dateEnd = $getVal('date-end');
$taxSerie = $getVal('tax-serie');

$taxNumberRaw = $getVal('tax-number');
$keysListRaw = $getVal('keys-list');

$taxNumber = array_filter(
    preg_split('/\R/', trim(str_replace("'", '', $taxNumberRaw))),
    fn($item) => $item !== ''
);

$keysList = array_filter(
    preg_split('/\R/', trim(str_replace("'", '', $keysListRaw))),
    fn($item) => $item !== ''
);

// Automatic tax type detection
if (count($keysList)) {
    $taxType = 'nfe';
    $message = 'Automaticamente alterado o tipo de nota para NFE para baixar pelas Chaves de Acesso!';
}

// Logic for contribution type defaults
if ($taxType === 'nfce') {
    $contribuitionType = '';
} elseif ($taxType === 'nfe' && $contribuitionType === '') {
    $contribuitionType = 'E';
}

// Persistence: Save current state to session
$persistFields = [
    'tax-type',
    'session',
    'js-session',
    'cd-user',
    'ie-emit',
    'contribuition-type',
    'date-start',
    'date-end',
    'tax-number',
    'keys-list',
    'tax-serie'
];
foreach ($persistFields as $field) {
    if (isset($_POST[$field])) {
        $_SESSION[$field] = $_POST[$field];
    }
}

$start = isset($_POST['start']);

$data = [
    'taxType'            => $taxType,
    'session'            => trim($session),
    'jsSession'          => trim($jsSession),
    'user'               => trim($user),
    'ieEmit'             => trim($ieEmit),
    'contribuition-type' => $contribuitionType,
    'dateStart'          => $dateStart,
    'dateEnd'            => $dateEnd,
    'taxNumber'          => $taxNumber,
    'taxSerie'           => $taxSerie,
    'keysList'           => $keysList,
    'start'              => $start
];

// Show UI
require_once __DIR__ . '/frontend.php';

if (!$start) {
    return;
}

// Dependency Injection Bootstrap (The Manual Way)
$dto = SubmitDTO::create($data);
$client = new HttpClient();
$ui = new UIResponder();

// O/C Principle
$provider = match ($dto->taxType) {
    'nfe'  => new NfeProvider($dto, $client),
    'nfce' => new NfceProvider($dto, $client),
    default => throw new Exception("Tipo de nota desconhecido: {$dto->taxType}")
};

// SRP
$service = new DownloaderService($dto, $provider, $ui);
$service->run();
