<?php

namespace App\Utils;

use ZipArchive;
use RuntimeException;

function unzip(): bool
{
    $extractPath = realpath(__DIR__ . '/../../Output/Extracted/');
    $outputPath = realpath(__DIR__ . '/../../Output/');

    if (!file_exists($extractPath)) {
        mkdir($extractPath, 0777, true);
    }

    $files = scandir($outputPath);
    if ($files === false) {
        throw new RuntimeException("Failed to scan directory");
    }

    $zip = new ZipArchive();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'zip') {
            continue;
        }

        $fullPath = realpath($outputPath . DIRECTORY_SEPARATOR . $file);

        if (!$zip->open($fullPath) === true) {
            echo "Failed to open: $file";
            return false;
        }

        $zip->extractTo($extractPath);
        $zip->close();
    }

    return true;
}
