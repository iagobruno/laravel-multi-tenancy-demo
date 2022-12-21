<?php

namespace App\Helpers;

function ensureFloatHasZeroAtEnd($val)
{
    $exploded = explode('.', $val);
    $exploded[1] = str_pad($exploded[1] ?? '', 2, '0');
    return implode('.', $exploded);
}
