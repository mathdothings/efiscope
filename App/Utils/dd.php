<?php

namespace App\Utils;

function dd(mixed $value): void
{
    echo '<br />';
    echo '<pre style="
        font-size: 1.15rem;
        padding: 1rem;
        background-color: #252526;
        color: yellow;
    ">';
    var_dump($value);
    echo '<pre />';
    echo '<br />';

    die;
}
