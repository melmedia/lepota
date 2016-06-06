<?php
namespace lepota\formatters;

class Url
{

    /**
     * Convert cyrillic text to valid url
     * @param string $text
     * @param string $charset
     * @return string
     */
    public static function fromText($text, $charset)
    {
        return mb_strtolower(
            str_replace(' ', '_', Transliteration::ru2Lat($text)),
            $charset
        );
    }

}
