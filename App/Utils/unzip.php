<?php

namespace App\Utils;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Recursively unzips all ZIP files found in the Output directory.
 * Extracts them into a parallel structure within Output/Extracted/.
 */
function unzip(): bool
{
    $baseOutputPath = realpath(__DIR__ . '/../../Output/');
    $downloadedPath = $baseOutputPath . DIRECTORY_SEPARATOR . 'Downloaded' . DIRECTORY_SEPARATOR;
    $baseExtractPath = $baseOutputPath . DIRECTORY_SEPARATOR . 'Extracted' . DIRECTORY_SEPARATOR;

    if (!file_exists($downloadedPath)) return true; // Nothing to unzip

    if (!file_exists($baseExtractPath)) {
        mkdir($baseExtractPath, 0777, true);
    }

    $directory = new RecursiveDirectoryIterator($downloadedPath);
    $iterator = new RecursiveIteratorIterator($directory);
    $zip = new ZipArchive();

    foreach ($iterator as $info) {
        if ($info->isDir() || strtolower($info->getExtension()) !== 'zip') {
            continue;
        }

        $fullPath = $info->getRealPath();

        // Skip files already in the Extracted directory
        if (str_contains($fullPath, $baseExtractPath)) {
            continue;
        }

        $res = $zip->open($fullPath);
        if ($res === true) {
            // Determine relative path starting from 'Downloaded/' to maintain same structure in 'Extracted/'
            $relativePath = str_replace($downloadedPath, '', dirname($fullPath) . DIRECTORY_SEPARATOR);
            $targetPath = $baseExtractPath . $relativePath;

            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }

            $zip->extractTo($targetPath);
            $zip->close();
        } else {
            echo "Falha ao abrir o arquivo zip: " . $info->getFilename() . " (Código do erro: $res)<br>";
            echo "Verifique se suas credenciais estão vencidas!<br>";
        }
    }

    return true;
}
