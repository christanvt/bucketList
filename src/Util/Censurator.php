<?php

namespace App\Util;


class Censurator
{
    const ForbiddenWords = ['pomme', 'truelle', 'quoi', 'hein', 'en fait', 'voilà'];

    public function purify(string $text): string
    {
        foreach (self::ForbiddenWords as $detected) {
            $replace = str_repeat("*", mb_strlen($detected));
            $text = str_ireplace($detected, $replace, $text);
        }
        return $text;
    }
}
