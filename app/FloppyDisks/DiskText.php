<?php

namespace App\FloppyDisks;

class DiskText
{
    public static function translate(string $contentKey, string $locale): string
    {
        $text = __($contentKey, [], $locale);

        return is_string($text) ? $text : $contentKey;
    }
}
