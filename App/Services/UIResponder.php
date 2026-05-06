<?php

namespace App\Services;

class UIResponder
{
    /**
     * Respond with a success message.
     *
     * @param int $count
     */
    public function success(int $count): void
    {
        echo '<p style="text-align: center; background-color: #F0FDF4; color: #00C951; padding: 0.5rem 1rem; border: 1px solid #B9F8CF; border-radius: 6px;">' . 'Foram encontrados: '  . $count . ' resultados!' . '</p>';
        ob_flush();
        flush();
    }

    /**
     * Respond with a session error message.
     */
    public function sessionError(): void
    {
        echo '<div id="session-error">';
        echo '<p style="text-align: center; background-color: #FFE2E2; color: #FB2C36; padding: 0.5rem 1rem; border: 1px solid #FFA2A2; border-radius: 6px;">Erro ao validar sessão!</p>';
        echo '</div>';
        ob_flush();
        flush();
    }

    /**
     * Inform about a download event.
     *
     * @param string $filepath
     */
    public function downloadFinished(string $filepath): void
    {
        echo '<p style="margin: 5px 0; text-align: center; background-color: #F0FDF4; color: #00C951; padding: 0.5rem 1rem; border: 1px solid #B9F8CF; border-radius: 6px;">' . 'Os arquivos foram baixados em: ' . realpath($filepath) . '</p>';
        ob_flush();
        flush();
    }

    /**
     * Inform about a search status.
     *
     * @param string $date
     * @param int $count
     * @param bool $isFull
     */
    public function searchStatus(string $date, int $count, bool $isFull = false): void
    {
        echo '<hr />';
        echo '<p>' . $date . ' (' . $count . ')' . '</p>';
        if ($isFull) {
            echo '<h3 style="background-color: #f52b37; color: white; padding: 1rem;"> Houveram mais de 500 registros em ' . $date . '</h3>';
        }
        ob_flush();
        flush();
    }

    /**
     * Log a message in real-time.
     *
     * @param string $message
     * @param string $color
     */
    public function log(string $message, string $color = 'black'): void
    {
        echo "<p style='color: {$color};'>{$message}</p>";
        ob_flush();
        flush();
    }

    /**
     * Render the scrap result table in real-time.
     *
     * @param \App\Scrapper\ScrapResult $result
     */
    public function renderDetails(\App\Scrapper\ScrapResult $result): void
    {
        if ($result->count() === 0) return;

        echo '<details>';
        echo '<summary>Detalhes</summary>';
        echo '<table>';
        echo '    <thead>';
        echo '        <tr>';
        echo '            <th>Chave de Acesso</th>';
        echo '            <th style="width: 100px">Nota</th>';
        echo '            <th style="width: 100px">Série</th>';
        echo '        </tr>';
        echo '    </thead>';
        echo '    <tbody>';

        foreach ($result->details as $item) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['key'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($item['number'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($item['serie'] ?? '') . '</td>';
            echo '</tr>';
        }

        echo '    </tbody>';
        echo '</table>';
        echo '</details>';
        ob_flush();
        flush();
    }
}
