<?php

namespace Zheltikov\Cms;

/**
 * @param array $array
 * @return bool
 */
function array_is_list(array $array): bool
{
    $expected_key = 0;
    foreach ($array as $i => $_) {
        if ($i !== $expected_key) {
            return false;
        }

        $expected_key++;
    }

    return true;
}
