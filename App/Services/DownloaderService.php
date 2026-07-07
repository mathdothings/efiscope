<?php

namespace App\Services;

use App\Contracts\InvoiceProvider;
use App\DTOs\FormSubmission\SubmitDTO;
use App\Services\UIResponder;
use DateTime;

class DownloaderService
{
    public function __construct(
        private readonly SubmitDTO $dto,
        private readonly InvoiceProvider $provider,
        private readonly UIResponder $ui
    ) {}

    /**
     * Start the download process based on DTO criteria.
     */
    public function run(): void
    {
        if (!$this->dto->start) {
            return;
        }

        try {
            if (count($this->dto->keysList)) {
                $this->processByKeys();
                return;
            }

            if (count($this->dto->taxNumber)) {
                $this->processByTaxNumbers();
                return;
            }

            $this->processByDateRange();
        } catch (\Exception $e) {
            $this->ui->log("Critical Error: " . $e->getMessage(), "red");
        }
    }

    private function retry(array $keysList): void
    {
        $this->ui->log("Reiniciando download de chaves falhas...", "blue");

        $maxRetries = 10;
        $failedKeys = [];

        foreach ($keysList as $index => $key) {
            $this->ui->log("Baixando chave " . ($index + 1) . " de " . count($keysList) . " :: Processando chave $key");

            $downloadSuccess = false;
            $lastError = null;

            // Retry loop for each key
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $filepath = $this->provider->downloadByKey($key);

                    // Check if download was successful
                    if ($filepath && file_exists($filepath) && filesize($filepath) > 0) {
                        $this->ui->downloadFinished($filepath);
                        $downloadSuccess = true;
                        break; // Success - exit retry loop
                    } else {
                        throw new \Exception("Download returned empty or invalid file");
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();

                    // Check if this error is retryable
                    $isConnectionReset = (
                        strpos($lastError, 'Connection reset by peer') !== false ||
                        strpos($lastError, 'recv failure') !== false ||
                        strpos($lastError, 'timeout') !== false ||
                        strpos($lastError, 'connection refused') !== false
                    );

                    // If not retryable, fail immediately
                    if (!$isConnectionReset && $attempt > 1) {
                        $this->ui->log("Erro não-retentável: $lastError", "red");
                        break;
                    }

                    // If this was the last attempt, log failure
                    if ($attempt === $maxRetries) {
                        $this->ui->log("Falha após $maxRetries tentativas: $lastError", "red");
                    } else {
                        // Exponential backoff: 1s, 2s, 4s, 8s, 16s, 32s, 60s...
                        $waitTime = min(pow(2, $attempt - 1), 60);
                        $this->ui->log("Tentativa $attempt/$maxRetries falhou. Aguardando {$waitTime}s antes de tentar novamente...", "gold");
                        sleep($waitTime);
                    }
                }
            }

            if (!$downloadSuccess) {
                $failedKeys[] = $key;
                $this->ui->log("Falha definitiva ao baixar chave: $key", "red");
            }

            // Wait between different keys
            if ($index < count($this->dto->keysList) - 1) {
                sleep(2);
            }
        }

        // Report summary
        if (!empty($failedKeys)) {
            $this->ui->log("\n" . count($failedKeys) . " chave(s) falharam:\n" . implode("\n", $failedKeys), "crimson");
        }

        $this->finish($this->dto->keysList);
    }

    private function processByKeys(): void
    {
        $this->ui->log("Iniciando download por Chaves de Acesso...", "blue");

        $maxRetries = 10;
        $failedKeys = [];

        foreach ($this->dto->keysList as $index => $key) {
            $this->ui->log("Baixando chave " . ($index + 1) . " de " . count($this->dto->keysList) . " :: Processando chave $key");

            $downloadSuccess = false;
            $lastError = null;

            // Retry loop for each key
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $filepath = $this->provider->downloadByKey($key);

                    // Check if download was successful
                    if ($filepath && file_exists($filepath) && filesize($filepath) > 0) {
                        $this->ui->downloadFinished($filepath);
                        $downloadSuccess = true;
                        break; // Success - exit retry loop
                    } else {
                        throw new \Exception("Download returned empty or invalid file");
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();

                    // Check if this error is retryable
                    $isConnectionReset = (
                        strpos($lastError, 'Connection reset by peer') !== false ||
                        strpos($lastError, 'recv failure') !== false ||
                        strpos($lastError, 'timeout') !== false ||
                        strpos($lastError, 'connection refused') !== false
                    );

                    // If not retryable, fail immediately
                    if (!$isConnectionReset && $attempt > 1) {
                        $this->ui->log("Erro não-retentável: $lastError", "red");
                        break;
                    }

                    // If this was the last attempt, log failure
                    if ($attempt === $maxRetries) {
                        $this->ui->log("Falha após $maxRetries tentativas: $lastError", "red");
                    } else {
                        // Exponential backoff: 1s, 2s, 4s, 8s, 16s, 32s, 60s...
                        $waitTime = min(pow(2, $attempt - 1), 60);
                        $this->ui->log("Tentativa $attempt/$maxRetries falhou. Aguardando {$waitTime}s antes de tentar novamente...", "gold");
                        sleep($waitTime);
                    }
                }
            }

            if (!$downloadSuccess) {
                $failedKeys[] = $key;
                $this->ui->log("Falha definitiva ao baixar chave: $key", "red");
            }

            // Wait between different keys
            if ($index < count($this->dto->keysList) - 1) {
                sleep(2);
            }
        }

        // Report summary
        if (!empty($failedKeys)) {
            $this->ui->log("\n" . count($failedKeys) . " chave(s) falharam:\n" . implode("\n", $failedKeys), "crimson");
            $this->retry($failedKeys);
        }

        $this->finish($this->dto->keysList);
    }

    private function processByTaxNumbers(): void
    {
        $this->ui->log("Iniciando busca por Números de Nota...", "blue");
        $allKeys = [];
        $date = (new DateTime())->format('d/m/Y');

        foreach ($this->dto->taxNumber as $number) {
            $this->ui->log("Buscando Nota: $number");

            // This is a bit hacky because Providers search for all notes on a date,
            // but we can pass the number via DTO as it's already doing in NfceProvider.

            $result = $this->provider->search($date);
            $this->ui->searchStatus($date, $result->count(), $result->isFull);
            $this->ui->renderDetails($result);

            if ($result->count() > 0) {
                $allKeys = array_merge($allKeys, $result->keys);
            }

            sleep(rand(5, 10));
        }

        if (count($allKeys) > 0) {
            $filepath = $this->provider->download($allKeys, "TAX_NUMBERS");
            $this->ui->downloadFinished($filepath);
        }

        $this->finish($allKeys);
    }

    private function processByDateRange(): void
    {
        $this->ui->log("Iniciando download por Intervalo de Datas...", "blue");
        $dates = $this->generateDateRange();
        $allKeys = [];

        $maxRetries = 10;

        foreach ($dates as $index => $date) {
            $downloadSuccess = false;
            $lastError = null;
            $filepath = null;

            // Retry loop for each key
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $result = $this->provider->search($date);
                    $this->ui->searchStatus($date, $result->count(), $result->isFull);
                    $this->ui->renderDetails($result);

                    if (count($result->keys) > 0) {
                        $dateObj = DateTime::createFromFormat('d/m/Y', $result->date);
                        $dateFormatted = $dateObj ? $dateObj->format('d-m-Y') : $result->date;
                        $filepath = $this->provider->download($result->keys, $dateFormatted);
                    } else {
                        $downloadSuccess = true; // No keys to download, but not a failure
                        break;
                    }

                    if ($filepath && file_exists($filepath) && filesize($filepath) > 0) {
                        $this->ui->downloadFinished($filepath);
                        $allKeys = array_merge($allKeys, $result->keys);
                        $downloadSuccess = true;
                        break;
                    } else {
                        throw new \Exception("Download returned empty or invalid file");
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();

                    // Check if this error is retryable
                    $isConnectionReset = (
                        strpos($lastError, 'Connection reset by peer') !== false ||
                        strpos($lastError, 'recv failure') !== false ||
                        strpos($lastError, 'timeout') !== false ||
                        strpos($lastError, 'connection refused') !== false
                    );

                    // If not retryable, fail immediately
                    if (!$isConnectionReset && $attempt > 1) {
                        if ($e instanceof \App\Exceptions\ExpiredSessionException) {
                            $this->ui->sessionError();
                            die;
                        }

                        $this->ui->log("Erro não retentável: $lastError", "red");
                        break;
                    }

                    // If this was the last attempt, log failure
                    if ($attempt === $maxRetries) {
                        $this->ui->log("Falha após $maxRetries tentativas: $lastError", "red");
                    } else {
                        // Exponential backoff: 1s, 2s, 4s, 8s, 16s, 32s, 60s...
                        $waitTime = min(pow(2, $attempt - 1), 60);
                        $this->ui->log("Tentativa $attempt/$maxRetries falhou. Aguardando {$waitTime}s antes de tentar novamente...", "gold");
                        sleep($waitTime);
                    }
                }
            }

            if (!$downloadSuccess) {
                $this->ui->log("Falha definitiva ao baixar as chaves do dia $date", "red");
            }

            if ($index + 1 !== count($dates)) {
                sleep(rand(3, 5));
            }
        }

        $this->finish($allKeys);
    }

    private function generateDateRange(): array
    {
        $start = new DateTime($this->dto->dateStart);
        $end = new DateTime($this->dto->dateEnd);
        $diff = $start->diff($end)->days + 1;

        $dates = [];
        for ($i = 0; $i < $diff; $i++) {
            $current = clone $start;
            $current->modify("+$i day");
            $dates[] = $current->format('d/m/Y');
        }

        return $dates;
    }

    private function finish(array $keys = []): void
    {
        $this->ui->success(count($keys));

        require_once realpath(__DIR__ . '/../Utils/unzip.php');
        require_once realpath(__DIR__ . '/../Utils/delete_all_files.php');

        $this->ui->log("Iniciando extração dos arquivos...", "blue");
        \App\Utils\unzip();
        $this->ui->log("Limpando arquivos temporários...", "blue");
        \App\Utils\delete_all_files();
        $this->ui->log("Processo concluído!");
    }
}
