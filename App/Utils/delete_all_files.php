<?php

namespace App\Utils;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Recursively deletes all .zip files within the Output folder, 
 * but ignores the 'Extracted' folder to protect extracted data.
 */
function delete_all_files(): void
{
    $basePath = realpath(__DIR__ . '/../../Output/');
    $downloadedPath = $basePath . DIRECTORY_SEPARATOR . 'Downloaded' . DIRECTORY_SEPARATOR;

    if (!file_exists($downloadedPath)) return;

    $directory = new RecursiveDirectoryIterator($downloadedPath);
    $iterator = new RecursiveIteratorIterator($directory);

    foreach ($iterator as $info) {
        if ($info->isDir() || strtolower($info->getExtension()) !== 'zip') {
            continue;
        }

        $filePath = $info->getRealPath();

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}
