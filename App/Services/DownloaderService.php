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

    private function processByKeys(): void
    {
        $this->ui->log("Iniciando download por Chaves de Acesso...", "blue");

        foreach ($this->dto->keysList as $index => $key) {
            $this->ui->log("Baixando chave " . ($index + 1) . " de " . count($this->dto->keysList) . " :: Processando chave $key");

            // Note: Currently BaseProvider doesn't have searchByKey, I'll use raw search for now
            // or refactor Providers to handle single key search.

            $filepath = $this->provider->downloadByKey($key);
            $this->ui->downloadFinished($filepath);

            if ($index < count($this->dto->keysList) - 1) {
                sleep(2);
            }
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

        foreach ($dates as $index => $date) {
            $result = $this->provider->search($date);
            $this->ui->searchStatus($date, $result->count(), $result->isFull);
            $this->ui->renderDetails($result);

            if ($result->count() > 0) {
                $dateObj = DateTime::createFromFormat('d/m/Y', $result->date);
                $dateFormatted = $dateObj ? $dateObj->format('d-m-Y') : $result->date;
                $filepath = $this->provider->download($result->keys, $dateFormatted);

                $this->ui->downloadFinished($filepath);
                $allKeys = array_merge($allKeys, $result->keys);
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
