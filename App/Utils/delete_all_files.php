<?php

namespace App\Utils;

function delete_all_files()
{
    $directory = __DIR__ . '/../../Output/';
    $files = scandir($directory);
    $path = '';

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $directory . $file;
        }

        if (is_file($path)) {
            unlink($path);
        }
    }
}
