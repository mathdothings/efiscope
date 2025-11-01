<?php

namespace App\Utils;

function delete_all_files()
{
    $directory = realpath(__DIR__ . '/../../Output/');
    $files = scandir($directory);
    $path = '';

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $file);
        }

        if (is_file($path)) {
            unlink($path);
        }
    }
}
