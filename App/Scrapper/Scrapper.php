<?php

namespace App\Scrapper;

use function App\Utils\dd;
use App\DTOs\FormSubmission\SubmitDTO;
use Dom\HTMLDocument;

final class Scrapper
{
    public string $date = '';
    public function __construct(private SubmitDTO $dto) {}

    public function scrap(string $response): array
    {
        $dom = HTMLDocument::createFromString($response);
        $error = $dom->getElementById('msgErro');

        if (strpos($error->innerHTML, 'ERRO')) {
            echo '<div id="session-error">';
            echo '<h3>Erro ao validar sessão!</h3>';
            echo '</div>';
            die;
        }

        $amount = $dom->querySelectorAll('.thickbox');
        $elements = $dom->querySelectorAll('.tabelaCadastroLinha');
        echo '<p>' . $this->date . ' (' . $amount->length . ')' . '</p>';

        if ($amount->length === 0) return [];
        if ($amount->length === 500) echo '<h3 style="background-color: #f52b37; color: white; padding: 1rem;"> Houveram mais de 500 registros em ' . $this->date . '</h1>';

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

        if ($this->dto->taxType === 'nfe') {
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

            if ($this->dto->taxType === 'nfe') {
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

        return $chaves;
    }
}
