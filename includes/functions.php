<?php
function stringToSecret(string $string = NULL)
{
    if (!$string) {
        return NULL;
    }
    $length = strlen($string);
    $visibleCount = (int) round($length / 4);
    $hiddenCount = $length - ($visibleCount * 2);
    return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}
